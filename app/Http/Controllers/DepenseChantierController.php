<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chantier\DepenseRequest;
use App\Models\Chantier;
use App\Models\DepensesChantier;
use App\Services\ChantierService;

class DepenseChantierController extends Controller
{
    public function __construct(
        private ChantierService $chantierService
    ) {}

    // Page 1 : Choisir un chantier
    public function index()
    {
        $chantiers = Chantier::withSum('depenses', 'montant')
            ->withCount('depenses')
            ->orderByRaw("FIELD(statut,
                                 'en_cours', 'en_attente', 'suspendu', 'livre')")
            ->orderBy('nomChantier')
            ->get();

        return view('direction.depenses.index', compact('chantiers'));
    }

    // Page 2 : Dépenses d'un chantier avec filtre dates
    public function show(Chantier $chantier)
    {
        $dateDebut = request(
            'date_debut',
            now()->startOfMonth()->toDateString()
        );
        $dateFin   = request(
            'date_fin',
            now()->toDateString()
        );

        $depenses = DepensesChantier::where('chantier_id', $chantier->id)
            ->whereBetween('date_depense', [$dateDebut, $dateFin])
            ->orderByDesc('date_depense')
            ->get();

        $stats = [
            'total'         => $depenses->sum('montant'),
            'nb'            => $depenses->count(),
            'par_categorie' => $depenses
                ->groupBy('categorie')
                ->map(fn($g) => $g->sum('montant')),
            'total_global'  => DepensesChantier::where('chantier_id', $chantier->id)
                ->sum('montant'),
        ];

        return view(
            'direction.depenses.show',
            compact('chantier', 'depenses', 'stats', 'dateDebut', 'dateFin')
        );
    }

    // Ajouter une dépense
    public function store(DepenseRequest $request, Chantier $chantier)
    {
        $this->chantierService->ajouterDepense($chantier, $request->validated());

        return back()->with('success', 'Dépense ajoutée avec succès.');
    }

    // Supprimer une dépense
    public function destroy(DepensesChantier $depense)
    {
        $this->chantierService->supprimerDepense($depense);
        return back()->with('success', 'Dépense supprimée avec succès.');
    }
}
