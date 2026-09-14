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

        // Une seule requête au lieu de trois (total / actifs / inactifs)
        // via une agrégation conditionnelle, portable quel que soit le
        // driver de base de données.
        $agregats = Ouvrier::selectRaw(
            "COUNT(*) as total,
             SUM(CASE WHEN statutOuvrier = 'actif' THEN 1 ELSE 0 END) as actifs,
             SUM(CASE WHEN statutOuvrier = 'inactif' THEN 1 ELSE 0 END) as inactifs"
        )->first();

        $stats = [
            'total'    => (int) $agregats->total,
            'actifs'   => (int) $agregats->actifs,
            'inactifs' => (int) $agregats->inactifs,
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

    /**
     * NB : le paramètre s'appelle "$personnel" (et non "$ouvrier") car la
     * route générée par Route::resource('personnel', ...) attend un
     * segment {personnel}. Le binding implicite de Laravel fait
     * correspondre le nom du paramètre de route au nom de l'argument ici
     * — un nom différent (ex: $ouvrier) casse silencieusement le binding
     * et update() devient un no-op (aucune erreur, mais rien n'est
     * sauvegardé). Voir toggleStatut() plus bas pour le cas où la route a
     * volontairement un nom de paramètre différent ({ouvrier}).
     */
    public function update(PersonnelRequest $request, Ouvrier $personnel)
    {
        $this->personnelService->modifier($personnel, $request->validated());

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', 'Ouvrier mis à jour avec succès.');
    }

    /**
     * Route dédiée : Route::patch('personnel/{ouvrier}/toggle', ...) — le
     * paramètre s'appelle bien "ouvrier" ici, donc $ouvrier est correct.
     * On réutilise directement l'instance retournée par le service (déjà
     * rafraîchie en interne) au lieu de rappeler fresh() sur la variable
     * locale : plus robuste (aucune dépendance à un binding parfait) et
     * évite une requête SQL supplémentaire inutile.
     */
    public function toggleStatut(Ouvrier $ouvrier)
    {
        $ouvrier = $this->personnelService->toggleStatut($ouvrier);

        $message = $ouvrier->statutOuvrier === 'actif'
            ? 'Ouvrier activé.'
            : 'Ouvrier désactivé.';

        return back()->with('success', $message);
    }

    public function destroy(Ouvrier $personnel)
    {
        // Suppression uniquement si inactif
        if ($personnel->statutOuvrier === 'actif') {
            return back()->with(
                'error',
                'Impossible de supprimer un ouvrier actif. '
                    . 'Désactivez-le d\'abord.'
            );
        }

        $nom = $personnel->nomOuvrier . ' ' . $personnel->prenomOuvrier;
        $personnel->delete();

        return redirect()
            ->route('direction.personnel.index')
            ->with('success', $nom . ' a été supprimé.');
    }
}
