<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tache\PhaseRequest;
use App\Http\Requests\Tache\TacheRequest;
use App\Models\Chantier;
use App\Models\Phase;
use App\Models\Tache;
use App\Models\User;
use App\Services\TacheService;

class TacheController extends Controller
{
    public function __construct(
        private TacheService $tacheService
    ) {}

    // ══════════════════════════════════════════════════════════
    // PHASES
    // ══════════════════════════════════════════════════════════

    public function indexPhases(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $chantier->load(['phases.taches']);

        return view('chef_projet.phases.index', compact('chantier'));
    }

    public function storePhase(PhaseRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        if ($chantier->statut === 'livre') {
            return back()->with(
                'error',
                'Impossible de planifier : ce chantier est livré.'
            );
        }

        $this->tacheService->creerPhase($request->validated(), $chantier->id);
        return back()->with('success', 'Phase créée avec succès.');
    }

    public function destroyPhase(Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        try {
            $this->tacheService->supprimerPhase($phase);
            return back()->with('success', 'Phase supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // TACHES
    // ══════════════════════════════════════════════════════════

    public function indexTaches(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $chantier->load(['phases.taches.responsable']);

        $tachesEnRetard = $chantier->taches()
            ->where('statutTache', '!=', 'terminee')
            ->where('date_fin_prevue', '<', now())
            ->count();

        return view(
            'chef_projet.taches.index',
            compact('chantier', 'tachesEnRetard')
        );
    }

    public function createTache(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        if ($chantier->statut === 'livre') {
            return redirect()
                ->route('chef_projet.taches.index', $chantier->id)
                ->with(
                    'error',
                    'Impossible de créer une tâche : ce chantier est livré.'
                );
        }

        $phases = $chantier->phases()->orderBy('ordre')->get();
        $taches = $chantier->taches()->orderBy('nomTache')->get();

        return view(
            'chef_projet.taches.create',
            compact('chantier', 'phases', 'taches')
        );
    }

    public function storeTache(TacheRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $this->tacheService->creerTache($request->validated(), $chantier->id);

        return redirect()
            ->route('chef_projet.taches.index', $chantier->id)
            ->with('success', 'Tâche créée avec succès.');
    }

    public function editTache(Chantier $chantier, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $phases = $chantier->phases()->orderBy('ordre')->get();
        $taches = $chantier->taches()
            ->where('id', '!=', $tache->id)
            ->orderBy('nomTache')
            ->get();

        return view(
            'chef_projet.taches.edit',
            compact('chantier', 'tache', 'phases', 'taches')
        );
    }

    public function updateTache(TacheRequest $request, Chantier $chantier, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $this->tacheService->modifierTache($tache, $request->validated());

        return redirect()
            ->route('chef_projet.taches.index', $chantier->id)
            ->with('success', 'Tâche mise à jour avec succès.');
    }

    public function mettreAJourAvancement(Chantier $chantier, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        request()->validate([
            'avancement' => 'required|integer|min:0|max:100',
        ]);

        try {
            $this->tacheService->mettreAJourAvancement($tache, (int) request('avancement'));
            return back()->with('success', 'Avancement mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changerStatutTache(Chantier $chantier, Tache $tache, string $statut)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $statuts = ['en_attente', 'en_cours', 'terminee'];

        if (!in_array($statut, $statuts)) {
            return back()->with('error', 'Statut invalide.');
        }

        try {
            $this->tacheService->changerStatut($tache, $statut);
            return back()->with('success', 'Statut mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyTache(Chantier $chantier, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        try {
            $this->tacheService->supprimerTache($tache);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->tacheService->supprimerTache($tache);

        return back()->with('success', 'Tâche supprimée avec succès.');
    }
}
