<?php

namespace App\Http\Controllers\Direction;

use App\Http\Controllers\Controller;
use App\Models\Approvisionnement;
use App\Services\ApprovisionnementService;

class DirectionApproController extends Controller
{
    public function __construct(
        protected ApprovisionnementService $approService
    ) {}

    public function index()
    {
        $demandesEnAttente = Approvisionnement::with(['chantier', 'demandeur'])
            ->where('statutAppro', 'en_attente')
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->orderBy('created_at')
            ->get();

        $demandesEnCours = Approvisionnement::with(['chantier', 'demandeur'])
            ->whereIn('statutAppro', ['validee', 'en_cours_livraison', 'partiellement_recue'])
            ->orderBy('created_at')
            ->get();

        $historique = Approvisionnement::with(['chantier', 'demandeur'])
            ->whereIn('statutAppro', ['rejetee', 'cloturee'])
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        return view(
            'direction.appro.index',
            compact('demandesEnAttente', 'demandesEnCours', 'historique')
        );
    }

    public function valider(Approvisionnement $demande)
    {
        $this->approService->valider($demande);
        return back()->with('success', 'Demande validée avec succès.');
    }

    public function rejeter(Approvisionnement $demande)
    {
        $this->approService->rejeter($demande);
        return back()->with('success', 'Demande rejetée.');
    }

    public function passerCommande(Approvisionnement $demande)
    {
        $this->approService->commander($demande);
        return back()->with('success', 'Commande passée — en cours de livraison.');
    }

    public function historique()
    {
        $dateDebut = request('date_debut', now()->startOfMonth()->toDateString());
        $dateFin   = request('date_fin', now()->toDateString());

        $demandes = Approvisionnement::with(['chantier', 'demandeur', 'rapportsEntrees'])
            ->whereIn('statutAppro', ['cloturee', 'rejetee'])
            ->whereBetween('updated_at', [
                $dateDebut . ' 00:00:00',
                $dateFin   . ' 23:59:59',
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('statut');

        $stats = [
            'cloturees' => $demandes->get('cloturee', collect())->count(),
            'rejetees'  => $demandes->get('rejetee', collect())->count(),
            'total'     => $demandes->flatten()->count(),
        ];

        return view(
            'direction.appro.historique',
            compact('demandes', 'stats', 'dateDebut', 'dateFin')
        );
    }
}
