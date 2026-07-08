<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chantier\AffectationRequest;
use App\Http\Requests\Chantier\ChantierRequest;
use App\Http\Requests\Chantier\DepenseRequest;
use App\Models\Chantier;
use App\Models\DepensesChantier;
use App\Models\User;
use App\Services\ChantierService;

class ChantierController extends Controller
{
    public function __construct(
        private ChantierService $chantierService
    ) {}

    // ── DIRECTION ─────────────────────────────────────────────

    public function index()
    {
        $chantiers = Chantier::with(['chefProjet', 'pointeur'])
            ->orderByRaw("FIELD(statut,
                                 'en_cours', 'en_attente', 'suspendu', 'livre')")
            ->get()
            ->groupBy('statut');

        $stats = [
            'total'      => Chantier::count(),
            'en_cours'   => Chantier::where('statut', 'en_cours')->count(),
            'en_attente' => Chantier::where('statut', 'en_attente')->count(),
            'livre'      => Chantier::where('statut', 'livre')->count(),
        ];

        return view('direction.chantiers.index', compact('chantiers', 'stats'));
    }

    public function create()
    {
        $chefsProjets = User::where('role', 'chef_projet')
            ->where('actif', true)
            ->orderBy('nomUser')
            ->get();

        return view('direction.chantiers.create', compact('chefsProjets'));
    }

    public function store(ChantierRequest $request)
    {
        $chantier = $this->chantierService->creer($request->validated());

        return redirect()
            ->route('direction.chantiers.show', $chantier->id)
            ->with('success', 'Chantier créé avec succès.');
    }

    public function show(Chantier $chantier)
    {
        $chantier->load(['chefProjet', 'pointeur', 'depenses', 'phases.taches']);

        $chefsProjets = User::where('role', 'chef_projet')
            ->where('actif', true)
            ->orderBy('nomUser')
            ->get();

        $pointeurs = User::where('role', 'pointeur')
            ->where('actif', true)
            ->orderBy('nomUser')
            ->get();

        return view(
            'direction.chantiers.show',
            compact('chantier', 'chefsProjets', 'pointeurs')
        );
    }

    public function edit(Chantier $chantier)
    {
        $chefsProjets = User::where('role', 'chef_projet')
            ->where('actif', true)
            ->orderBy('nomUser')
            ->get();

        return view(
            'direction.chantiers.edit',
            compact('chantier', 'chefsProjets')
        );
    }

    public function update(ChantierRequest $request, Chantier $chantier)
    {
        $this->chantierService->modifier($chantier, $request->validated());

        return redirect()
            ->route('direction.chantiers.show', $chantier->id)
            ->with('success', 'Chantier mis à jour avec succès.');
    }

    public function destroy(Chantier $chantier)
    {
        try {
            $this->chantierService->supprimer($chantier);
            return redirect()
                ->route('direction.chantiers.index')
                ->with('success', 'Chantier supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Affecter chef de projet
    public function affecterChefProjet(AffectationRequest $request, Chantier $chantier)
    {
        $this->chantierService->affecterChefProjet(
            $chantier,
            $request->chef_projet_id
        );

        return back()->with('success', 'Chef de projet mis à jour avec succès.');
    }

    // Affecter pointeur
    public function affecterPointeur(AffectationRequest $request, Chantier $chantier)
    {
        $this->chantierService->affecterPointeur(
            $chantier,
            $request->pointeur_id
        );

        return back()->with('success', 'Pointeur mis à jour avec succès.');
    }

    // Changer le statut
    public function changerStatut(Chantier $chantier, string $statut)
    {
        try {
            $this->chantierService->changerStatut($chantier, $statut);
            return back()->with('success', 'Statut mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Ajouter une dépense
    public function ajouterDepense(DepenseRequest $request, Chantier $chantier)
    {
        $this->chantierService->ajouterDepense($chantier, $request->validated());

        return back()->with('success', 'Dépense ajoutée avec succès.');
    }

    // Supprimer une dépense
    public function supprimerDepense(Chantier $chantier, DepensesChantier $depense)
    {
        $this->chantierService->supprimerDepense($depense);

        return back()->with('success', 'Dépense supprimée avec succès.');
    }

    // ── CHEF DE PROJET ────────────────────────────────────────

    public function indexChefProjet()
    {
        $chantiers = Chantier::with(['pointeur', 'phases', 'taches'])
            ->where('chef_projet_id', auth()->id())
            ->orderByRaw("FIELD(statut,
                             'en_cours', 'en_attente', 'suspendu', 'livre')")
            ->get()
            ->groupBy('statut');

        $stats = [
            'total'    => Chantier::where('chef_projet_id', auth()->id())->count(),
            'en_cours' => Chantier::where('chef_projet_id', auth()->id())
                ->where('statut', 'en_cours')->count(),
            'livre'    => Chantier::where('chef_projet_id', auth()->id())
                ->where('statut', 'livre')->count(),
        ];

        return view('chef_projet.chantiers.index', compact('chantiers', 'stats'));
    }

    public function showChefProjet(Chantier $chantier)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);

        $chantier->load(['pointeur', 'phases.taches', 'depenses']);

        return view('chef_projet.chantiers.show', compact('chantier'));
    }
}
