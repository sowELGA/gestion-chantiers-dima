<?php

namespace App\Http\Controllers\Direction;

use App\Http\Requests\Personnel\PersonnelRequest;
use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Ouvrier;
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
        $recherche  = request('recherche');

        $query = Ouvrier::with(['poste', 'chantier'])
            ->orderBy('nomOuvrier');

        if ($chantierId) {
            $query->where('chantier_id', $chantierId);
        }

        if ($statut !== 'tous') {
            $query->where('statutOuvrier', $statut);
        }

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('nomOuvrier', 'like', '%' . $recherche . '%')
                    ->orWhere('prenomOuvrier', 'like', '%' . $recherche . '%');
            });
        }

        $personnel = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Ouvrier::count(),
            'actifs'   => Ouvrier::where('statutOuvrier', 'actif')->count(),
            'inactifs' => Ouvrier::where('statutOuvrier', 'inactif')->count(),
        ];

        $chantiers = Chantier::orderBy('nomChantier')->get();

        return view(
            'direction.personnel.index',
            compact(
                'personnel',
                'stats',
                'chantiers',
                'chantierId',
                'statut',
                'recherche'
            )
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

    public function edit(Ouvrier $personnel)
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

    public function update(PersonnelRequest $request, Ouvrier $ouvrier)
    {
        $this->personnelService->modifier($ouvrier, $request->validated());

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', 'Ouvrier mis à jour avec succès.');
    }

    public function toggleStatut(Ouvrier $ouvrier)
    {
        $this->personnelService->toggleStatut($ouvrier);

        $message = $ouvrier->fresh()->statutOuvrier === 'actif'
            ? 'Ouvrier activé.'
            : 'Ouvrier désactivé.';

        return back()->with('success', $message);
    }

    public function destroy(Ouvrier $ouvrier)
    {
        // Suppression uniquement si inactif
        if ($ouvrier->statutOuvrier === 'actif') {
            return back()->with(
                'error',
                'Impossible de supprimer un ouvrier actif. '
                    . 'Désactivez-le d\'abord.'
            );
        }

        $nom = $ouvrier->nomOuvrier . ' ' . $ouvrier->prenomOuvrier;
        $ouvrier->delete();

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', $nom . ' a été supprimé.');
    }
}
