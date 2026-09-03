<?php

namespace App\Http\Controllers\Pointeur;

use App\Helpers\SemaineHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pointage\ModifierJourRequest;
use App\Http\Requests\Pointage\PointageRequest;
use App\Models\Chantier;
use App\Models\Pointage;
use App\Services\PointageService;
use App\Services\RecapService;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function __construct(
        private PointageService $pointageService,
        private RecapService $recapService
    ) {}

    public function ficheJour()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        if ($chantier->statut !== 'en_cours') {
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
                $chantier->id,
                $this->recapService
            );
            return redirect()
                ->route('pointeur.pointage.recap')
                ->with('success', 'Fiche du jour enregistrée. Le récap a été mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function recapSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        if (!in_array($chantier->statut, ['en_cours', 'suspendu'])) {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));
        $page    = (int) request('page', 1);

        $infos    = $this->recapService->getInfosSemaine($semaine, $annee);
        $statut   = $this->recapService->getStatutSemaine($chantier->id, $semaine, $annee);
        $donnees  = $this->recapService->getLignesRecap($chantier->id, $semaine, $annee, $page);
        $totaux   = $this->recapService->getTotauxSemaine($chantier->id, $semaine, $annee);
        $semaines = $this->recapService->getSemainesDisponibles(5);

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
            'semaines'    => $semaines,
        ]);
    }

    public function soumettreSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));

        $this->recapService->soumettreSemaine($chantier->id, auth()->id(), $semaine, $annee);

        return redirect()
            ->route('pointeur.pointage.recap', ['semaine' => $semaine, 'annee' => $annee])
            ->with('success', 'Fiche soumise au chef de projet.');
    }

    public function modifierJour(string $date)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $dateCarbon = Carbon::parse($date);
        $semaine    = SemaineHelper::numeroCycle($dateCarbon);
        $annee      = SemaineHelper::anneeCycle($dateCarbon);
        $statut     = $this->recapService->getStatutSemaine($chantier->id, $semaine, $annee);

        if ($statut['statut'] !== 'rejetee') {
            return redirect()
                ->route('pointeur.pointage.recap', ['semaine' => $semaine, 'annee' => $annee])
                ->with('error', 'Cette fiche n\'est pas modifiable.');
        }

        $tousPersonnel = $this->pointageService->getToutPersonnel($chantier->id);
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

    public function enregistrerModificationJour(ModifierJourRequest $request)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        try {
            $this->pointageService->modifierPointageJour(
                $chantier->id,
                $request->date,
                $request->validated()['pointages'],
                $this->recapService
            );

            $page = (int) request('page', 1);
            return redirect()
                ->route('pointeur.pointage.modifier-jour', [
                    'date' => $request->date,
                    'page' => $page,
                ])
                ->with('success', 'Pointage du ' . Carbon::parse($request->date)->locale('fr')->isoFormat('dddd D MMMM') . ' mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
