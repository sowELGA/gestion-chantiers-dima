<?php

namespace App\Http\Controllers;

use App\Helpers\SemaineHelper;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use App\Services\PdfService;
use Carbon\Carbon;

class RecapHebdomadaireController extends Controller
{
    public function __construct(
        private PdfService $pdfService
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

        // 52 semaines disponibles avec libellé Samedi → Vendredi
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
    // APERÇU — Détail d'une fiche avant PDF
    // ══════════════════════════════════════════════════════════

    public function apercu(Chantier $chantier)
    {
        $semaine  = (int) request('semaine', Carbon::today()->isoWeek());
        $annee    = (int) request('annee',   Carbon::today()->year);
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        // Récaps groupés par poste, triés alphabétiquement
        $tousRecaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statut', ['validee_cp', 'envoyee_direction'])
            ->get();

        $groupes = $this->regrouperParCorpsMetier($tousRecaps);

        // Colonnes Sam → Ven
        $colonnes = collect(range(0, 6))->map(
            fn($i) => $samedi->copy()->addDays($i)->startOfDay()
        );

        $statut        = $tousRecaps->first()?->statut;
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

    // ══════════════════════════════════════════════════════════
    // PDF — Génération de la fiche de paie
    // ══════════════════════════════════════════════════════════

    public function genererPdf(Chantier $chantier)
    {
        $semaine = (int) request('semaine');
        $annee   = (int) request('annee');

        // Vérifier que la fiche est bien transmise à la direction
        $existe = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statut', 'envoyee_direction')
            ->exists();

        if (!$existe) {
            return back()->with(
                'error',
                'La fiche de paie n\'est pas encore disponible.'
            );
        }

        return $this->pdfService->genererFichePaie(
            $chantier->id,
            $semaine,
            $annee
        );
    }

    private function regrouperParCorpsMetier(
        \Illuminate\Support\Collection $recaps
    ): \Illuminate\Support\Collection {
        return $recaps
            ->groupBy(function ($recap) {
                $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                // Extraire la famille : retirer "chef ", "aide ", etc.
                $famille = preg_replace(
                    '/^(chef|aide|sous[\s-]chef|premier)\s+/i',
                    '',
                    $poste
                );
                return ucfirst(trim($famille));
            })
            ->map(function ($lignes, $famille) {
                // Trier : chef en premier, puis membres
                return $lignes->sortBy(function ($recap) {
                    $poste = strtolower($recap->ouvrier->poste->libelle ?? '');
                    if (str_starts_with($poste, 'chef')) return 0;
                    if (str_starts_with($poste, 'aide')) return 2;
                    return 1;
                })->values();
            })
            ->sortKeys();
    }
}
