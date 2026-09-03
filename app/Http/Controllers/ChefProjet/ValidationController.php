<?php

namespace App\Http\Controllers\ChefProjet;

use App\Helpers\SemaineHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pointage\RejetRecapRequest;
use App\Models\Chantier;
use App\Services\RecapService;
use Carbon\Carbon;

class ValidationController extends Controller
{
    public function __construct(
        private RecapService $recapService
    ) {}

    public function validationChefProjet(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));
        $page    = (int) request('page', 1);

        $infos    = $this->recapService->getInfosSemaine($semaine, $annee);
        $statut   = $this->recapService->getStatutSemaine($chantier->id, $semaine, $annee);
        $donnees  = $this->recapService->getLignesRecap($chantier->id, $semaine, $annee, $page);
        $totaux   = $this->recapService->getTotauxSemaine($chantier->id, $semaine, $annee);
        $semaines = $this->recapService->getSemainesDisponibles(5);

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
            'semaines'    => $semaines,
        ]);
    }

    public function valider(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = (int) request('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) request('annee', SemaineHelper::anneeCycle($today));

        $this->recapService->validerSemaine($chantier->id, $semaine, $annee, auth()->id());

        return back()->with('success', 'Fiche validée et transmise à la direction.');
    }

    public function rejeter(RejetRecapRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $today   = Carbon::today();
        $semaine = (int) $request->input('semaine', SemaineHelper::numeroCycle($today));
        $annee   = (int) $request->input('annee', SemaineHelper::anneeCycle($today));

        $this->recapService->rejeterSemaine(
            $chantier->id,
            $semaine,
            $annee,
            auth()->id(),
            $request->motif_rejet
        );

        return back()->with('success', 'Fiche rejetée. Le pointeur peut corriger.');
    }
}
