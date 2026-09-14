<?php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tache\PhaseRequest;
use App\Http\Requests\Tache\TacheRequest;
use App\Models\Chantier;
use App\Models\Phase;
use App\Models\Tache;
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

        $statutFiltre = request('statut', 'actives');

        $phases = Phase::with(['taches'])
            ->where('chantier_id', $chantier->id)
            ->orderBy('ordre')
            ->get();

        $phasesAffichees = match ($statutFiltre) {
            'toutes'     => $phases,
            'en_attente' => $phases->where('statutPhase', 'en_attente')->values(),
            'en_cours'   => $phases->where('statutPhase', 'en_cours')->values(),
            'terminee'   => $phases->where('statutPhase', 'terminee')->values(),
            default      => $phases->whereIn('statutPhase', ['en_attente', 'en_cours'])->values(),
        };

        return view(
            'chef_projet.phases.index',
            compact('chantier', 'phases', 'phasesAffichees', 'statutFiltre')
        );
    }

    public function createPhase(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        $this->verifierChantierModifiable($chantier);

        $prochainOrdre = Phase::where('chantier_id', $chantier->id)
            ->max('ordre') + 1;

        return view(
            'chef_projet.phases.create',
            compact('chantier', 'prochainOrdre')
        );
    }

    public function storePhase(PhaseRequest $request, Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        $this->verifierChantierModifiable($chantier);

        $this->tacheService->creerPhase(
            $request->validated(),
            $chantier->id
        );

        return redirect()
            ->route('chef_projet.phases.index', $chantier->id)
            ->with('success', 'Phase créée avec succès.');
    }

    public function editPhase(Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);

        return view('chef_projet.phases.edit', compact('chantier', 'phase'));
    }

    public function updatePhase(PhaseRequest $request, Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        $this->verifierChantierModifiable($chantier);

        try {
            $this->tacheService->modifierPhase($phase, $request->validated());
            return redirect()
                ->route('chef_projet.phases.index', $chantier->id)
                ->with('success', 'Phase modifiée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyPhase(Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        $this->verifierChantierModifiable($chantier);

        try {
            $this->tacheService->supprimerPhase($phase);
            return redirect()
                ->route('chef_projet.phases.index', $chantier->id)
                ->with('success', 'Phase supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // TÂCHES
    // ══════════════════════════════════════════════════════════

    public function indexTaches(Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);

        $taches = Tache::with(['tachePrecedente'])
            ->where('phase_id', $phase->id)
            ->orderBy('date_debut_prevue')
            ->get();

        return view(
            'chef_projet.taches.index',
            compact('chantier', 'phase', 'taches')
        );
    }

    public function createTache(Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        $this->verifierChantierModifiable($chantier);

        $tachesDisponibles = Tache::where('phase_id', $phase->id)->get();

        return view(
            'chef_projet.taches.create',
            compact('chantier', 'phase', 'tachesDisponibles')
        );
    }

    public function storeTache(TacheRequest $request, Chantier $chantier, Phase $phase)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        $this->verifierChantierModifiable($chantier);
        $data                   = $request->validated();
        $data['responsable_id'] = auth()->id();
        $data['phase_id']       = $phase->id;

        $this->tacheService->creerTache($data, $chantier->id);

        return redirect()
            ->route('chef_projet.phases.taches.index', [$chantier->id, $phase->id])
            ->with('success', 'Tâche créée avec succès.');
    }

    public function editTache(Chantier $chantier, Phase $phase, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        abort_if($tache->phase_id !== $phase->id, 404);

        if ($tache->statutTache === 'terminee') {
            return redirect()
                ->route('chef_projet.phases.taches.index', [$chantier->id, $phase->id])
                ->with('error', 'Impossible de modifier une tâche terminée.');
        }

        $tachesDisponibles = Tache::where('phase_id', $phase->id)
            ->where('id', '!=', $tache->id)
            ->get();

        return view(
            'chef_projet.taches.edit',
            compact('chantier', 'phase', 'tache', 'tachesDisponibles')
        );
    }

    public function updateTache(
        TacheRequest $request,
        Chantier $chantier,
        Phase $phase,
        Tache $tache
    ) {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        abort_if($tache->phase_id !== $phase->id, 404);
        $this->verifierChantierModifiable($chantier);

        $data                   = $request->validated();
        $data['responsable_id'] = auth()->id();
        $data['phase_id']       = $phase->id;

        try {
            $this->tacheService->modifierTache($tache, $data);
            return redirect()
                ->route('chef_projet.phases.taches.index', [$chantier->id, $phase->id])
                ->with('success', 'Tâche modifiée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyTache(Chantier $chantier, Phase $phase, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        abort_if($tache->phase_id !== $phase->id, 404);
        $this->verifierChantierModifiable($chantier);

        try {
            $this->tacheService->supprimerTache($tache);
            return redirect()
                ->route('chef_projet.phases.taches.index', [$chantier->id, $phase->id])
                ->with('success', 'Tâche supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function mettreAJourAvancement(Chantier $chantier, Phase $phase, Tache $tache)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($phase->chantier_id !== $chantier->id, 404);
        abort_if($tache->phase_id !== $phase->id, 404);
        $this->verifierChantierModifiable($chantier);

        request()->validate(['avancement' => 'required|integer|min:0|max:100']);

        try {
            $this->tacheService->mettreAJourAvancement($tache, (int) request('avancement'));
            return back()->with('success', 'Avancement mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // HELPER PRIVÉ
    // ══════════════════════════════════════════════════════════

    private function verifierChantierModifiable(Chantier $chantier): void
    {
        if ($chantier->statut === 'livre') {
            abort(403, 'Ce chantier est livré — la planification est verrouillée.');
        }
    }
}
