<?php

namespace App\Http\Controllers;

use App\Http\Requests\Personnel\PersonnelRequest;
use App\Models\Chantier;
use App\Models\Personnel;
use App\Models\Poste;
use App\Services\PersonnelService;

class PersonnelController extends Controller
{
    public function __construct(
        private PersonnelService $personnelService
    ) {}

    public function index()
    {
        $chantierId = request('chantier_id');
        $statut     = request('statut', 'tous');

        $query = Personnel::with(['poste', 'chantier'])
            ->orderBy('nomPersonnel');

        if ($chantierId) {
            $query->where('chantier_id', $chantierId);
        }

        if ($statut !== 'tous') {
            $query->where('statutPersonnel', $statut);
        }

        $personnel = $query->paginate(10);

        $stats = [
            'total'    => Personnel::count(),
            'actifs'   => Personnel::where('statutPersonnel', 'actif')->count(),
            'inactifs' => Personnel::where('statutPersonnel', 'inactif')->count(),
        ];

        $chantiers = Chantier::orderBy('nomChantier')->get();

        return view(
            'direction.personnel.index',
            compact('personnel', 'stats', 'chantiers', 'chantierId', 'statut')
        );
    }

    public function create()
    {
        $postes    = Poste::orderBy('libelle')->get();
        $chantiers = Chantier::whereIn('statut', ['en_attente', 'en_cours'])
            ->orderBy('nomChantier')
            ->get();

        return view('direction.personnel.create', compact('postes', 'chantiers'));
    }

    public function store(PersonnelRequest $request)
    {
        $this->personnelService->creer($request->validated());

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', 'Ouvrier ajouté avec succès.');
    }

    public function edit(Personnel $personnel)
    {
        $postes    = Poste::orderBy('libelle')->get();
        $chantiers = Chantier::whereIn('statut', ['en_attente', 'en_cours'])
            ->orderBy('nomChantier')
            ->get();

        return view(
            'direction.personnel.edit',
            compact('personnel', 'postes', 'chantiers')
        );
    }

    public function update(PersonnelRequest $request, Personnel $personnel)
    {
        $this->personnelService->modifier($personnel, $request->validated());

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', 'Ouvrier mis à jour avec succès.');
    }

    public function toggleStatut(Personnel $personnel)
    {
        $this->personnelService->toggleStatut($personnel);

        $message = $personnel->fresh()->statutPersonnel === 'actif'
            ? 'Ouvrier activé avec succès.'
            : 'Ouvrier désactivé avec succès.';

        return back()->with('success', $message);
    }
}
