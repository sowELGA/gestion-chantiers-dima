<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pointage\ModifierJourRequest;
use App\Http\Requests\Pointage\PointageRequest;
use App\Http\Requests\Pointage\RejetRecapRequest;
use App\Models\Chantier;
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

        if (!in_array($chantier->statut, ['en_cours', 'suspendu'])) {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        $page       = (int) request('page', 1);
        $donnees    = $this->pointageService->getPointagesDuJour($chantier->id);
        $personnel  = $this->pointageService->getPersonnelPagine($chantier->id, $page);
        $modifiable = $chantier->statut === 'en_cours'
            && $this->pointageService->semaineModifiable($chantier->id);

        return view('pointeur.pointage.fiche', [
            'chantier'   => $chantier,
            'date'       => $donnees['date'],
            'pointages'  => $donnees['pointages'],
            'personnel'  => $personnel['personnel'],
            'pagination' => $personnel['pagination'],
            'modifiable' => $modifiable,
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

        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;
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

        // Vérifier que la semaine est bien rejetée
        $semaine = Carbon::parse($date)->isoWeek();
        $annee   = Carbon::parse($date)->year;
        $statut  = $this->pointageService->getStatutSemaine($chantier->id, $semaine, $annee);

        if ($statut['statut'] !== 'rejetee') {
            return redirect()
                ->route('pointeur.pointage.recap')
                ->with('error', 'Cette fiche n\'est pas dans un état modifiable.');
        }

        $page    = (int) request('page', 1);
        $donnees = $this->pointageService->getPointagesDuJourPagines(
            $chantier->id,
            $date,
            $page
        );

        $dateCarbon = Carbon::parse($date);

        return view('pointeur.pointage.modifier-jour', [
            'chantier'    => $chantier,
            'date'        => $dateCarbon,
            'lignes'      => $donnees['lignes'],
            'pagination'  => $donnees['pagination'],
            'motif_rejet' => $statut['motif_rejet'],
            'semaine'     => $semaine,
            'annee'       => $annee,
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

    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET — Validation
    // ══════════════════════════════════════════════════════════

    public function validationChefProjet(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);
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

    public function validerSemaine(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $this->pointageService->validerSemaine(
            $chantier->id,
            request('semaine'),
            request('annee'),
            auth()->id()
        );

        return back()->with('success', 'Fiche validée et transmise à la direction.');
    }

    public function rejeterSemaine(RejetRecapRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $this->pointageService->rejeterSemaine(
            $chantier->id,
            request('semaine'),
            request('annee'),
            auth()->id(),
            $request->motif_rejet
        );

        return back()->with('success', 'Fiche rejetée. Le pointeur peut la corriger.');
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — Récap et calcul
    // ══════════════════════════════════════════════════════════

    public function recapDirection()
    {
        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);

        $chantiers = Chantier::whereNotNull('pointeur_id')
            ->whereIn('statut', ['en_cours', 'suspendu'])
            ->get()
            ->map(function ($c) use ($semaine, $annee) {
                $page    = (int) request('page_' . $c->id, 1);
                $infos   = $this->pointageService->getInfosSemaine($semaine, $annee);
                $statut  = $this->pointageService->getStatutSemaine($c->id, $semaine, $annee);
                $donnees = $this->pointageService->getLignesRecap($c->id, $semaine, $annee, $page);
                $totaux  = $this->pointageService->getTotauxSemaine($c->id, $semaine, $annee);

                return [
                    'chantier'   => $c,
                    'jours'      => $infos['jours'],
                    'lignes'     => $donnees['lignes'],
                    'pagination' => $donnees['pagination'],
                    'statut'     => $statut['statut'],
                    'totaux'     => $totaux,
                ];
            });

        $semaines = collect();
        for ($i = 0; $i <= 11; $i++) {
            $date = Carbon::today()->subWeeks($i);
            $semaines->push([
                'semaine' => $date->isoWeek(),
                'annee'   => $date->year,
                'label'   => 'Semaine ' . $date->isoWeek() . ' — '
                    . $date->copy()->startOfWeek()->subDays(2)->format('d/m') . ' au '
                    . $date->copy()->startOfWeek()->subDays(2)->addDays(6)->format('d/m/Y'),
            ]);
        }

        return view(
            'direction.pointage.recap',
            compact('chantiers', 'semaine', 'annee', 'semaines')
        );
    }

    public function calculerSalaires(Chantier $chantier)
    {
        $semaine = request('semaine');
        $annee   = request('annee');

        $this->pointageService->calculerSalaires($chantier->id, $semaine, $annee);

        return back()->with('success', 'Salaires calculés. Vous pouvez générer la fiche de paie.');
    }
}
