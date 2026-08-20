<?php

namespace App\Http\Controllers;

use App\Helpers\SemaineHelper;
use App\Http\Requests\Pointage\ModifierJourRequest;
use App\Http\Requests\Pointage\PointageRequest;
use App\Http\Requests\Pointage\RejetRecapRequest;
use App\Models\Chantier;
use App\Models\Pointage;
use App\Models\RecapHebdomadaire;
use App\Services\PointageService;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function __construct(
        private PointageService $pointageService
    ) {}

    // ══════════════════════════════════════════════════════════
    // POINTEUR — Fiche journalière
    // ══════════════════════════════════════════════════════════


    public function ficheJour()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        if ($chantier->statut !=  'en_cours') {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        $donnees       = $this->pointageService->getPointagesDuJour($chantier->id);
        $tousPersonnel = $this->pointageService->getToutPersonnel($chantier->id);

        $modifiable = $chantier->statut === 'en_cours'
            && $this->pointageService->semaineModifiable($chantier->id);

        return view('pointeur.pointage.fiche', [
            'chantier'      => $chantier,
            'date'          => $donnees['date'],
            'pointages'     => $donnees['pointages'],
            'tousPersonnel' => $tousPersonnel,
            'modifiable'    => $modifiable,
        ]);
    }

    public function enregistrerFiche(PointageRequest $request)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        try {
            $this->pointageService->enregistrerFiche(
                $request->validated()['pointages'],
                $chantier->id
            );
            return redirect()
                ->route('pointeur.pointage.recap')
                ->with('success', 'Fiche du jour enregistrée. Le récap a été mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // POINTEUR — Récap hebdomadaire
    // ══════════════════════════════════════════════════════════

    public function recapSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        if (!in_array($chantier->statut, ['en_cours', 'suspendu'])) {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        $today   = Carbon::today();
        $semaine = SemaineHelper::numeroCycle($today);
        $annee   = SemaineHelper::anneeCycle($today);
        $page    = (int) request('page', 1);

        $infos   = $this->pointageService->getInfosSemaine($semaine, $annee);
        $statut  = $this->pointageService->getStatutSemaine($chantier->id, $semaine, $annee);
        $donnees = $this->pointageService->getLignesRecap($chantier->id, $semaine, $annee, $page);
        $totaux  = $this->pointageService->getTotauxSemaine($chantier->id, $semaine, $annee);

        $modifiable  = $statut['statut'] === 'rejetee';
        $soumettable = in_array($statut['statut'], ['en_attente', 'rejetee'])
            && $chantier->statut === 'en_cours';

        return view('pointeur.pointage.recap', [
            'chantier'    => $chantier,
            'semaine'     => $infos['semaine'],
            'annee'       => $infos['annee'],
            'debut'       => $infos['debut'],
            'fin'         => $infos['fin'],
            'jours'       => $infos['jours'],
            'lignes'      => $donnees['lignes'],
            'pagination'  => $donnees['pagination'],
            'statut'      => $statut['statut'],
            'motif_rejet' => $statut['motif_rejet'],
            'totaux'      => $totaux,
            'modifiable'  => $modifiable,
            'soumettable' => $soumettable,
        ]);
    }

    public function soumettreSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $this->pointageService->soumettreSemaine($chantier->id, auth()->id());

        return redirect()
            ->route('pointeur.pointage.recap')
            ->with('success', 'Fiche soumise au chef de projet.');
    }

    // ══════════════════════════════════════════════════════════
    // POINTEUR — Modification par jour (récap rejeté)
    // ══════════════════════════════════════════════════════════

    // Affiche un seul jour à modifier avec pagination des ouvriers
    public function modifierJour(string $date)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $semaine = Carbon::parse($date)->isoWeek();
        $annee   = Carbon::parse($date)->year;
        $statut  = $this->pointageService->getStatutSemaine(
            $chantier->id,
            $semaine,
            $annee
        );

        if ($statut['statut'] !== 'rejetee') {
            return redirect()
                ->route('pointeur.pointage.recap')
                ->with('error', 'Cette fiche n\'est pas modifiable.');
        }

        // Tous les ouvriers groupés par poste (même ordre que la fiche)
        $tousPersonnel = $this->pointageService->getToutPersonnel($chantier->id);

        // Tous les pointages du jour
        $pointagesJour = Pointage::where('chantier_id', $chantier->id)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('ouvrier_id');

        return view('pointeur.pointage.modifier-jour', [
            'chantier'      => $chantier,
            'date'          => Carbon::parse($date),
            'tousPersonnel' => $tousPersonnel,
            'pointagesJour' => $pointagesJour,
            'motif_rejet'   => $statut['motif_rejet'],
            'semaine'       => $semaine,
            'annee'         => $annee,
        ]);
    }

    // Enregistre les modifications d'un jour
    public function enregistrerModificationJour(ModifierJourRequest $request)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        try {
            $this->pointageService->modifierPointageJour(
                $chantier->id,
                $request->date,
                $request->validated()['pointages']
            );

            // Rester sur le même jour, page suivante ou recap si terminé
            $page = (int) request('page', 1);
            return redirect()
                ->route('pointeur.pointage.modifier-jour', [
                    'date' => $request->date,
                    'page' => $page,
                ])
                ->with('success', 'Pointage du '
                    . Carbon::parse($request->date)->locale('fr')->isoFormat('dddd D MMMM')
                    . ' mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
