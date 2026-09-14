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
        try {
            $this->approService->valider($demande);
            return back()->with('success', 'Demande validée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejeter(Approvisionnement $demande)
    {
        try {
            $this->approService->rejeter($demande);
            return back()->with('success', 'Demande rejetée.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * La date de livraison prévue est optionnelle ici : le pop-up permet
     * de la saisir tout de suite, mais on peut valider sans elle et la
     * renseigner plus tard via definirDateLivraison().
     */
    public function passerCommande(Approvisionnement $demande)
    {
        $data = request()->validate([
            'date_livraison_prevue' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            $this->approService->commander($demande, $data['date_livraison_prevue'] ?? null);
            return back()->with('success', 'Commande passée — en cours de livraison.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Permet de définir ou corriger la date de livraison prévue à tout
     * moment tant que la commande n'est pas clôturée — y compris après
     * l'avoir passée sans date, ou pour corriger une date déjà saisie.
     */
    public function definirDateLivraison(Approvisionnement $demande)
    {
        $data = request()->validate([
            'date_livraison_prevue' => 'nullable|date',
        ]);

        try {
            $this->approService->definirDateLivraisonPrevue($demande, $data['date_livraison_prevue'] ?? null);
            return back()->with('success', 'Date de livraison prévue mise à jour.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function historique()
    {
        $filtres = request()->validate([
            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',
        ]);

        $dateDebut = $filtres['date_debut'] ?? now()->startOfMonth()->toDateString();
        $dateFin   = $filtres['date_fin'] ?? now()->toDateString();

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
