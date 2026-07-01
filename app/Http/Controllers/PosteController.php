<?php

namespace App\Http\Controllers;

use App\Http\Requests\Personnel\PosteRequest;
use App\Models\Poste;
use App\Services\PosteService;

class PosteController extends Controller
{
    public function __construct(
        private PosteService $posteService
    ) {}

    public function index()
    {
        $postes = Poste::withCount('personnel')
            ->orderBy('libelle')
            ->get();

        return view('direction.postes.index', compact('postes'));
    }

    public function store(PosteRequest $request)
    {
        $this->posteService->creer($request->validated());

        return back()->with('success', 'Poste créé avec succès.');
    }

    public function update(PosteRequest $request, Poste $poste)
    {
        $this->posteService->modifier($poste, $request->validated());

        return back()->with('success', 'Poste mis à jour avec succès.');
    }

    public function destroy(Poste $poste)
    {
        try {
            $this->posteService->supprimer($poste);
            return back()->with('success', 'Poste supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
