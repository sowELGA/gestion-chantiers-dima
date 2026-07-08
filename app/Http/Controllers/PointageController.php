<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pointage\ModifierJourRequest;
use App\Http\Requests\Pointage\PointageRequest;
use App\Http\Requests\Pointage\RejetRecapRequest;
use App\Models\Chantier;
use App\Services\PointageService;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function __construct(
        private PointageService $pointageService
    ) {}

    // ══════════════════════════════════════════════════════════
    // POINTEUR
    // ══════════════════════════════════════════════════════════

    // Page 1 : Fiche journalière
    public function ficheJour()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        // Bloquer si chantier pas en cours
        if (!in_array($chantier->statut, ['en_cours', 'suspendu'])) {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        // Si suspendu, lecture seule
        $chantierActif = $chantier->statut === 'en_cours';

        $fiche      = $this->pointageService->getFicheDuJour($chantier->id);
        $modifiable = $chantierActif
            && $this->pointageService->semaineModifiable($chantier->id);

        return view(
            'pointeur.pointage.fiche',
            compact('chantier', 'fiche', 'modifiable', 'chantierActif')
        );
    }

    // Enregistrer la fiche journalière
    public function enregistrerFiche(PointageRequest $request)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        try {
            $this->pointageService->enregistrerFiche(
                $request->validated(),
                $chantier->id
            );
            return redirect()
                ->route('pointeur.pointage.recap')
                ->with('success', 'Fiche du jour enregistrée. Le récap a été mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Page 2 : Récap semaine en cours
    public function recapSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        if (!in_array($chantier->statut, ['en_cours', 'suspendu'])) {
            return view('pointeur.pointage.bloque', compact('chantier'));
        }

        $semaine = Carbon::today()->isoWeek();
        $annee   = Carbon::today()->year;

        $recap = $this->pointageService->getRecapSemaine(
            $chantier->id,
            $semaine,
            $annee
        );

        // Modifiable UNIQUEMENT si rejeté par le chef de projet
        $modifiable = $recap['statut'] === 'rejetee';

        // Soumettable si en_attente ou rejeté
        $soumettable = in_array($recap['statut'], ['en_attente', 'rejetee'])
            && $chantier->statut === 'en_cours';

        return view(
            'pointeur.pointage.recap',
            compact('chantier', 'recap', 'modifiable', 'soumettable')
        );
    }

    // Modifier un jour depuis le récap rejeté
    public function modifierJourDepuisRecap(ModifierJourRequest $request)
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        try {
            $this->pointageService->modifierPointageJour(
                $chantier->id,
                $request->date,
                $request->pointages
            );
            return back()->with(
                'success',
                'Pointage du ' .
                    Carbon::parse($request->date)->locale('fr')->isoFormat('dddd D MMMM') .
                    ' mis à jour.'
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Soumettre le récap au chef de projet
    public function soumettreSemaine()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $this->pointageService->soumettreSemaine(
            $chantier->id,
            auth()->id()
        );

        return redirect()
            ->route('pointeur.pointage.recap')
            ->with('success', 'Fiche soumise au chef de projet avec succès.');
    }

    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET
    // ══════════════════════════════════════════════════════════

    // Récap temps réel + validation
    public function validationChefProjet(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);

        $recap = $this->pointageService->getRecapSemaine(
            $chantier->id,
            $semaine,
            $annee
        );

        return view(
            'chef_projet.pointage.validation',
            compact('chantier', 'recap', 'semaine', 'annee')
        );
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
    // DIRECTION
    // ══════════════════════════════════════════════════════════

    // Récap temps réel de tous les chantiers
    public function recapDirection()
    {
        $semaine = request('semaine', Carbon::today()->isoWeek());
        $annee   = request('annee', Carbon::today()->year);

        $chantiers = Chantier::whereNotNull('pointeur_id')
            ->whereIn('statut', ['en_cours', 'suspendu'])
            ->with(['chefProjet', 'pointeur'])
            ->get()
            ->map(fn($c) => [
                'chantier' => $c,
                'recap'    => $this->pointageService->getRecapSemaine(
                    $c->id,
                    $semaine,
                    $annee
                ),
            ]);

        // Semaines disponibles (12 semaines en arrière)
        $semaines = collect();
        for ($i = 0; $i <= 11; $i++) {
            $date = Carbon::today()->subWeeks($i);
            $semaines->push([
                'semaine' => $date->isoWeek(),
                'annee'   => $date->year,
                'label'   => 'Semaine ' . $date->isoWeek() . ' — ' .
                    $date->startOfWeek()->format('d/m') . ' au ' .
                    $date->endOfWeek()->format('d/m/Y'),
            ]);
        }

        return view(
            'direction.pointage.recap',
            compact('chantiers', 'semaine', 'annee', 'semaines')
        );
    }

    // Calculer les salaires
    public function calculerSalaires(Chantier $chantier)
    {
        $semaine = request('semaine');
        $annee   = request('annee');

        $this->pointageService->calculerSalaires(
            $chantier->id,
            $semaine,
            $annee
        );

        return back()->with(
            'success',
            'Salaires calculés avec succès. Vous pouvez générer la fiche de paie.'
        );
    }
}
