<?php

namespace App\Http\Controllers\ChefProjet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Approvisionnement\ApprovisionnementRequest;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Services\ApprovisionnementService;

class ChefApproController extends Controller
{
    public function __construct(
        protected ApprovisionnementService $approService
    ) {}

    public function create()
    {
        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->whereIn('statut', ['en_cours', 'en_attente'])
            ->orderBy('nomChantier')
            ->get();

        return view('chef_projet.appro.create', compact('chantiers'));
    }

    public function store(ApprovisionnementRequest $request)
    {
        $this->approService->creerPlusieurs($request->validated(), auth()->id());

        return redirect()
            ->route('chef_projet.appro.index')
            ->with('success', 'Demandes d\'approvisionnement enregistrées avec succès.');
    }

    public function index()
    {
        // Validées explicitement plutôt que passées brutes à
        // whereBetween() : une valeur malformée dans l'URL (favori,
        // partage de lien) provoquerait sinon une erreur SQL opaque.
        $filtres = request()->validate([
            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',
            'statut'     => 'nullable|string',
        ]);

        $dateDebut = $filtres['date_debut'] ?? now()->startOfMonth()->toDateString();
        $dateFin   = $filtres['date_fin'] ?? now()->toDateString();
        $statut    = $filtres['statut'] ?? 'tous';

        $demandes = Approvisionnement::with(['chantier'])
            ->whereHas('chantier', fn($q) => $q->where('chef_projet_id', auth()->id()))
            ->whereBetween('created_at', [
                $dateDebut . ' 00:00:00',
                $dateFin   . ' 23:59:59',
            ])
            ->when($statut !== 'tous', fn($q) => $q->where('statutAppro', $statut))
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Une seule requête agrégée au lieu de 4 requêtes whereHas
        // séparées portant chacune la même sous-clause de propriétaire.
        $brut = Approvisionnement::whereHas('chantier', fn($q) => $q->where('chef_projet_id', auth()->id()))
            ->selectRaw("
                SUM(CASE WHEN statutAppro = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statutAppro IN ('validee','en_cours_livraison','partiellement_recue') THEN 1 ELSE 0 END) as en_cours_livraison,
                SUM(CASE WHEN statutAppro = 'cloturee' THEN 1 ELSE 0 END) as cloturee,
                SUM(CASE WHEN statutAppro = 'rejetee' THEN 1 ELSE 0 END) as rejetee
            ")
            ->first();

        $stats = [
            'en_attente'         => (int) $brut->en_attente,
            'en_cours_livraison' => (int) $brut->en_cours_livraison,
            'cloturee'           => (int) $brut->cloturee,
            'rejetee'            => (int) $brut->rejetee,
        ];

        return view(
            'chef_projet.appro.index',
            compact('demandes', 'stats', 'dateDebut', 'dateFin', 'statut')
        );
    }

    public function edit(Chantier $chantier, Approvisionnement $demande)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($demande->chantier_id !== $chantier->id, 404);

        if ($demande->statutAppro !== 'en_attente') {
            return redirect()
                ->route('chef_projet.appro.index')
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->whereIn('statut', ['en_cours', 'en_attente'])
            ->orderBy('nomChantier')
            ->get();

        return view('chef_projet.appro.edit', compact('chantiers', 'demande'));
    }

    public function update(ApprovisionnementRequest $request, Chantier $chantier, Approvisionnement $demande)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($demande->chantier_id !== $chantier->id, 404);

        try {
            $this->approService->modifier($demande, $request->validated());
            return redirect()
                ->route('chef_projet.appro.index')
                ->with('success', 'Demande modifiée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Chantier $chantier, Approvisionnement $demande)
    {
        abort_if($chantier->chef_projet_id !== auth()->id(), 403);
        abort_if($demande->chantier_id !== $chantier->id, 404);

        try {
            $this->approService->supprimer($demande);
            return redirect()
                ->route('chef_projet.appro.index')
                ->with('success', 'Demande supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
