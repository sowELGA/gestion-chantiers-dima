<?php

namespace App\Http\Controllers\Direction;

use App\Helpers\PointageHelper;
use App\Helpers\SemaineHelper;
use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\RecapHebdomadaire;
use App\Services\PdfService;
use App\Services\RecapService;
use Carbon\Carbon;

class SalaireController extends Controller
{
    public function __construct(
        private RecapService $recapService,
        private PdfService $pdfService
    ) {}

    public function recapDirection()
    {
        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);

        $chantiers = Chantier::whereNotNull('pointeur_id')
            ->where('statut', 'en_cours')
            ->orderBy('nomChantier')
            ->get();

        $chantierId = request()->has('chantier_id') && request('chantier_id') !== ''
            ? (int) request('chantier_id')
            : null;

        $chantierSelectionne = $chantierId ? $chantiers->firstWhere('id', $chantierId) : null;
        $donneesChantier = null;

        if ($chantierSelectionne) {
            $page    = (int) request('page', 1);
            $infos   = $this->recapService->getInfosSemaine($semaine, $annee);
            $statut  = $this->recapService->getStatutSemaine($chantierId, $semaine, $annee);
            $donnees = $this->recapService->getLignesRecap($chantierId, $semaine, $annee, $page);
            $totaux  = $this->recapService->getTotauxSemaine($chantierId, $semaine, $annee);

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

        return view('direction.salaires.recap', compact(
            'chantiers',
            'chantierId',
            'donneesChantier',
            'semaine',
            'annee'
        ));
    }

    public function index()
    {
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle(Carbon::today()));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle(Carbon::today()));

        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        $chantiers = Chantier::with([
            'recapsHebdomadaires' => fn($q) => $q
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->whereIn('statutRecap', ['validee_cp', 'envoyee_direction'])
                ->with('ouvrier'),
        ])
            ->whereHas(
                'recapsHebdomadaires',
                fn($q) => $q
                    ->where('semaine', $semaine)
                    ->where('annee', $annee)
                    ->whereIn('statutRecap', ['validee_cp', 'envoyee_direction'])
            )
            ->get()
            ->map(function ($chantier) use ($semaine, $annee) {
                $recaps = $chantier->recapsHebdomadaires;

                // Une seule requête pour TOUS les pointages du chantier
                // cette semaine (au lieu d'une requête par ouvrier) : le
                // salaire n'est jamais stocké, il est recalculé à la volée
                // à partir des pointages réels + du taux figé sur chacun.
                $pointagesSemaine = PointageHelper::pointagesSemaine($chantier->id, $semaine, $annee);

                $totalSalaires = $recaps->sum(function ($recap) use ($pointagesSemaine) {
                    $pointagesOuvrier = $pointagesSemaine->get($recap->ouvrier_id, collect());
                    return PointageHelper::calculerSalaireDepuisPointages($pointagesOuvrier)['salaire_total'];
                });

                return [
                    'chantier'       => $chantier,
                    'nb_ouvriers'    => $recaps->count(),
                    'total_salaires' => $totalSalaires,
                    'statut'         => $recaps->first()?->statutRecap ?? 'validee_cp',
                ];
            });

        $semaines = $this->recapService->getSemainesDisponibles(10);

        return view('direction.salaires.recaps', compact(
            'chantiers',
            'semaine',
            'annee',
            'semaines',
            'samedi',
            'vendredi'
        ));
    }

    public function apercu(Chantier $chantier)
    {
        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));

        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        $statut = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->value('statutRecap');

        if ($statut === 'validee_cp') {
            $this->recapService->calculerSalaires($chantier->id, $semaine, $annee);
            $statut = RecapHebdomadaire::where('chantier_id', $chantier->id)
                ->where('semaine', $semaine)
                ->where('annee', $annee)
                ->value('statutRecap');
        }

        // Une seule requête pour tous les pointages de la semaine, groupée par
        // ouvrier — évite à la vue de refaire une requête Pointage par ligne.
        $pointagesSemaine = PointageHelper::pointagesSemaine($chantier->id, $semaine, $annee);

        $tousRecaps = RecapHebdomadaire::with(['ouvrier.poste'])
            ->where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->whereIn('statutRecap', ['validee_cp', 'envoyee_direction'])
            ->get()
            ->map(function ($recap) use ($pointagesSemaine) {
                // Détail jour par jour de cet ouvrier, prêt à afficher.
                // Provient de $pointagesSemaine, chargée UNE SEULE FOIS pour
                // tout le chantier plus haut : aucune requête par ouvrier.
                $pointagesOuvrier = $pointagesSemaine
                    ->get($recap->ouvrier_id, collect())
                    ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

                // Tout ce qui est chiffré est calculé à la volée depuis les
                // pointages (jamais stocké sur le récap) et attaché
                // dynamiquement au modèle, pour que la vue puisse continuer
                // à lire $recap->jours_presents / total_heures_sup /
                // salaire_base / salaire_heures_sup / salaire_total.
                $salaire = PointageHelper::calculerSalaireDepuisPointages($pointagesOuvrier->values());

                // Poste à afficher : celui enregistré (figé) lors des
                // pointages de la semaine, jamais le poste actuel de
                // l'ouvrier — qui a pu changer depuis.
                $recap->poste = PointageHelper::posteDepuisPointages($recap->ouvrier, $pointagesOuvrier);

                $recap->jours_presents     = $salaire['jours_presents'];
                $recap->total_heures_sup   = $salaire['total_heures_sup'];
                $recap->salaire_base       = $salaire['salaire_base'];
                $recap->salaire_heures_sup = $salaire['salaire_heures_sup'];
                $recap->salaire_total      = $salaire['salaire_total'];
                $recap->pointagesParJour   = $pointagesOuvrier;

                return $recap;
            });

        $groupes = $this->regrouperParCorpsMetier($tousRecaps);

        $colonnes = collect(range(0, 6))->map(fn($i) => $samedi->copy()->addDays($i)->startOfDay());

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
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));

        $existe = RecapHebdomadaire::where('chantier_id', $chantier->id)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->where('statutRecap', 'envoyee_direction')
            ->exists();

        if (!$existe) {
            return back()->with('error', 'La fiche de paie n\'est pas disponible.');
        }

        return $this->pdfService->genererFichePaie($chantier->id, $semaine, $annee);
    }

    private function regrouperParCorpsMetier(\Illuminate\Support\Collection $recaps): \Illuminate\Support\Collection
    {
        // Le regroupement (et le tri chef/membre/aide) se base sur le poste
        // ENREGISTRÉ LORS DU POINTAGE ($recap->poste, attaché dynamiquement
        // dans apercu()), jamais sur le poste actuel de l'ouvrier — qui a
        // pu changer depuis la semaine concernée.
        return $recaps
            ->groupBy(function ($recap) {
                $poste = strtolower($recap->poste->libelle ?? '');
                $famille = preg_replace('/^(chef|aide|sous[\s-]chef|premier)\s+/i', '', $poste);
                return ucwords(trim($famille));
            })
            ->map(fn($lignes) => $lignes->sortBy(function ($recap) {
                $poste = strtolower($recap->poste->libelle ?? '');
                if (str_starts_with($poste, 'chef')) return 0;
                if (str_starts_with($poste, 'aide')) return 2;
                return 1;
            })->values())
            ->sortKeys();
    }
}
