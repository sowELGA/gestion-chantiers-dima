<?php

namespace App\Http\Controllers;

use App\Helpers\SemaineHelper;
use App\Http\Requests\Pointage\RejetRecapRequest;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use App\Services\PdfService;
use App\Services\PointageService;
use Carbon\Carbon;

class RecapHebdomadaireController extends Controller
{
    public function __construct(
        private PointageService $pointageService,
        private PdfService      $pdfService
    ) {}

    // ══════════════════════════════════════════════════════════
    // HELPER — Samedi → Vendredi
    // ══════════════════════════════════════════════════════════

    private function getSamedi(int $annee, int $semaine): Carbon
    {
        return Carbon::now()
            ->setISODate($annee, $semaine)
            ->startOfWeek()
            ->subDays(2);
    }

    private function getVendredi(int $annee, int $semaine): Carbon
    {
        return $this->getSamedi($annee, $semaine)->copy()->addDays(6);
    }


    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET — Valider / Rejeter
    // ══════════════════════════════════════════════════════════
    public function validationChefProjet(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);
        $page    = (int) request('page', 1);

        $infos   = $this->pointageService->getInfosSemaine($semaine, $annee);
        $statut  = $this->pointageService->getStatutSemaine($chantier->id, $semaine, $annee);
        $donnees = $this->pointageService->getLignesRecap($chantier->id, $semaine, $annee, $page);
        $totaux  = $this->pointageService->getTotauxSemaine($chantier->id, $semaine, $annee);

        return view('chef_projet.pointage.validation', [
            'chantier'    => $chantier,
            'semaine'     => $infos['semaine'],
            'annee'       => $infos['annee'],
            'debut'       => $infos['debut'],
            'fin'         => $infos['fin'],
            'jours'       => $infos['jours'],
            'lignes'      => $donnees['lignes'],
            'pagination'  => $donnees['pagination'],
            'statut'      => $statut['statut'],
            'totaux'      => $totaux,
        ]);
    }

    public function valider(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        $this->pointageService->validerSemaine(
            $chantier->id,
            $semaine,
            $annee,
            auth()->id()
        );

        return back()->with('success', 'Fiche validée et transmise à la direction.');
    }

    public function rejeter(RejetRecapRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        $this->pointageService->rejeterSemaine(
            $chantier->id,
            $semaine,
            $annee,
            auth()->id(),
            $request->motif_rejet
        );

        return back()->with('success', 'Fiche rejetée. Le pointeur peut corriger.');
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — Récap multi-chantiers
    // ══════════════════════════════════════════════════════════

    public function recapDirection()
    {
        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        $chantiers = Chantier::whereNotNull('pointeur_id')
            ->where('statut', 'en_cours')
            ->orderBy('nomChantier')
            ->get();

        // On récupère la valeur saisie, sinon null
        $chantierId = request()->has('chantier_id') && request('chantier_id') !== ''
            ? (int) request('chantier_id')
            : null;

        $chantierSelectionne = $chantierId ? $chantiers->firstWhere('id', $chantierId) : null;

        $donneesChantier = null;
        if ($chantierSelectionne) {
            $page    = (int) request('page', 1);
            $infos   = $this->pointageService->getInfosSemaine($semaine, $annee);
            $statut  = $this->pointageService->getStatutSemaine(
                $chantierId,
                $semaine,
                $annee
            );
            $donnees = $this->pointageService->getLignesRecap(
                $chantierId,
                $semaine,
                $annee,
                $page
            );
            $totaux  = $this->pointageService->getTotauxSemaine(
                $chantierId,
                $semaine,
                $annee
            );

            $donneesChantier = [
                'chantier'   => $chantierSelectionne,
                'jours'      => $infos['jours'],
                'debut'      => $infos['debut'],
                'fin'        => $infos['fin'],
                'lignes'     => $donnees['lignes'],
                'pagination' => $donnees['pagination'],
                'statut'     => $statut['statut'],
                'totaux'     => $totaux,
                'semaine'    => $semaine,
                'annee'      => $annee,
            ];
        }

        return view('direction.pointage.recap', compact(
            'chantiers',
            'chantierId',
            'donneesChantier',
            'semaine',
            'annee'
        ));
    }

    // ══════════════════════════════════════════════════════════
    // LISTE — Fiches validées (index)
    // ══════════════════════════════════════════════════════════

    public function index()
    {
        $semaine  = (int) request('semaine', Carbon::today()->isoWeek());
        $annee    = (int) request('annee',   Carbon::today()->year);
        $samedi   = $this->getSamedi($annee, $semaine);
        $vendredi = $this->getVendredi($annee, $semaine);

        // Chantiers ayant des récaps validés CP ou transmis direction
        $chantiers = Chantier::with([
            'recapsHebdomadaires' => fn($q) => $q
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->whereIn('statut', ['validee_cp', 'envoyee_direction']),
        ])
            ->whereHas(
                'recapsHebdomadaires',
                fn($q) => $q
                    ->where('semaine', $semaine)
                    ->where('annee', $annee)
                    ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            )
            ->get()
            ->map(function ($chantier) use ($semaine, $annee) {
                $recaps = $chantier->recapsHebdomadaires;
                return [
                    'chantier'       => $chantier,
                    'nb_ouvriers'    => $recaps->count(),
                    'total_salaires' => $recaps->sum('salaire_total'),
                    'statut'         => $recaps->first()?->statut ?? 'validee_cp',
                ];
            });

        // semaines disponibles avec libellé Samedi → Vendredi
        $semaines = collect();
        for ($i = 0; $i <= 9; $i++) {
            $date = Carbon::today()->subWeeks($i);
            $s    = (int) $date->isoWeek();
            $a    = (int) $date->year;
            $sam  = $this->getSamedi($a, $s);
            $ven  = $this->getVendredi($a, $s);

            $semaines->push([
                'semaine' => $s,
                'annee'   => $a,
                'label'   => 'Semaine ' . $s
                    . ' — ' . $sam->locale('fr')->isoFormat('D MMM')
                    . ' au ' . $ven->locale('fr')->isoFormat('D MMM YYYY'),
            ]);
        }

        return view('direction.salaires.recaps', compact(
            'chantiers',
            'semaine',
            'annee',
            'semaines',
            'samedi',
            'vendredi'
        ));
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — Aperçu fiche de paie + calcul salaires
    // ══════════════════════════════════════════════════════════

    public function apercu(Chantier $chantier)
    {
        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee',   SemaineHelper::anneeCycle($today));

        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        // Vérifier statut
        $statut = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->value('statut');

        // Si validée CP mais salaires pas encore calculés → calculer maintenant
        if ($statut === 'validee_cp') {
            $this->pointageService->calculerSalaires(
                $chantier->id,
                $semaine,
                $annee
            );
            // Recharger le statut
            $statut = RecapHebdomadaire::where('chantier_id', $chantier->id)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->value('statut');
        }

        $tousRecaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            ->get();

        $groupes = $this->regrouperParCorpsMetier($tousRecaps);

        $colonnes = collect(range(0, 6))->map(
            fn($i) => $samedi->copy()->addDays($i)->startOfDay()
        );

        $totalGeneral  = $tousRecaps->sum('salaire_total');
        $totalPresents = $tousRecaps->sum('jours_presents');
        $totalHSup     = $tousRecaps->sum('total_heures_sup');
        $debutSemaine  = $samedi->locale('fr')->isoFormat('D MMMM YYYY');
        $finSemaine    = $vendredi->locale('fr')->isoFormat('D MMMM YYYY');

        return view('direction.salaires.apercu', compact(
            'chantier',
            'groupes',
            'colonnes',
            'semaine',
            'annee',
            'samedi',
            'vendredi',
            'debutSemaine',
            'finSemaine',
            'totalGeneral',
            'totalPresents',
            'totalHSup',
            'statut'
        ));
    }

    public function genererPdf(Chantier $chantier)
    {
        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee',   SemaineHelper::anneeCycle($today));

        $existe = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'envoyee_direction')
            ->exists();

        if (!$existe) {
            return back()->with('error', 'La fiche de paie n\'est pas disponible.');
        }

        return $this->pdfService->genererFichePaie(
            $chantier->id,
            $semaine,
            $annee
        );
    }

    // ══════════════════════════════════════════════════════════
    // HELPER PRIVÉ — Regroupement corps de métier
    // ══════════════════════════════════════════════════════════

    private function regrouperParCorpsMetier(
        \Illuminate\Support\Collection $recaps
    ): \Illuminate\Support\Collection {
        return $recaps
            ->groupBy(function ($recap) {
                $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                $famille = preg_replace(
                    '/^(chef|aide|sous[\s-]chef|premier)\s+/i',
                    '',
                    $poste
                );
                return ucwords(trim($famille));
            })
            ->map(fn($lignes) => $lignes->sortBy(function ($recap) {
                $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                if (str_starts_with($poste, 'chef')) return 0;
                if (str_starts_with($poste, 'aide')) return 2;
                return 1;
            })->values())
            ->sortKeys();
    }
}
