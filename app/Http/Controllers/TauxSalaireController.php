<?php

namespace App\Http\Controllers;

use App\Http\Requests\Personnel\TauxSalaireRequest;
use App\Models\Chantier;
use App\Services\TauxSalaireService;

class TauxSalaireController extends Controller
{
    public function __construct(
        private TauxSalaireService $tauxSalaireService
    ) {}

    // Liste des chantiers pour sélectionner lequel configurer
    public function index()
    {
        $chantiers = Chantier::whereIn('statut', ['en_attente', 'en_cours'])
            ->withCount('tauxSalaires')
            ->orderBy('nomChantier')
            ->get();

        return view('direction.salaires.taux.index', compact('chantiers'));
    }

    // Matrice des taux pour un chantier donné
    public function edit(Chantier $chantier)
    {
        $matrice = $this->tauxSalaireService->getMatriceTaux($chantier->id);

        return view('direction.salaires.taux.edit', compact('chantier', 'matrice'));
    }

    public function update(TauxSalaireRequest $request, Chantier $chantier)
    {
        $this->tauxSalaireService->enregistrerTaux(
            $chantier->id,
            $request->taux
        );

        return back()->with('success', 'Taux salariaux enregistrés avec succès.');
    }
}
