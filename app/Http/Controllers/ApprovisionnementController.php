<?php

namespace App\Http\Controllers;

use App\Http\Requests\Approvisionnement\ApprovisionnementRequest;
use App\Http\Requests\Approvisionnement\ReceptionRequest;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\RapportsEntree;
use App\Services\ApprovisionnementService;

class ApprovisionnementController extends Controller
{
    public function __construct(
        private ApprovisionnementService $approService
    ) {}

    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET
    // ══════════════════════════════════════════════════════════

    // Formulaire création
    public function create()
    {
        $chantiers = Chantier::where('chef_projet_id', auth()->id())
            ->whereIn('statut', ['en_cours', 'en_attente'])
            ->orderBy('nomChantier')
            ->get();

        return view('chef_projet.appro.create', compact('chantiers'));
    }

    // Enregistrer la demande
    public function store(ApprovisionnementRequest $request)
    {
        $this->approService->creer($request->validated(), auth()->id());

        return redirect()
            ->route('chef_projet.appro.index')
            ->with('success', 'Demande créée avec succès.');
    }

    // Liste des demandes du chef de projet
    public function indexChefProjet()
    {
        $dateDebut = request('date_debut', now()->startOfMonth()->toDateString());
        $dateFin   = request('date_fin', now()->toDateString());
        $statut    = request('statut', 'tous');

        $demandes = Approvisionnement::with(['chantier'])
            ->whereHas(
                'chantier',
                fn($q) =>
                $q->where('chef_projet_id', auth()->id())
            )
            ->whereBetween('created_at', [
                $dateDebut . ' 00:00:00',
                $dateFin   . ' 23:59:59',
            ])
            ->when($statut !== 'tous', fn($q) => $q->where('statut', $statut))
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'en_attente'          => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', auth()->id())
            )
                ->where('statut', 'en_attente')->count(),
            'en_cours_livraison'  => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', auth()->id())
            )
                ->whereIn('statut', ['validee', 'en_cours_livraison', 'partiellement_recue'])
                ->count(),
            'cloturee'            => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', auth()->id())
            )
                ->where('statut', 'cloturee')->count(),
            'rejetee'             => Approvisionnement::whereHas(
                'chantier',
                fn($q) => $q->where('chef_projet_id', auth()->id())
            )
                ->where('statut', 'rejetee')->count(),
        ];

        return view(
            'chef_projet.appro.index',
            compact('demandes', 'stats', 'dateDebut', 'dateFin', 'statut')
        );
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION
    // ══════════════════════════════════════════════════════════

    // Liste toutes les demandes
    public function indexDirection()
    {
        $demandesEnAttente = Approvisionnement::with(['chantier', 'demandeur'])
            ->where('statut', 'en_attente')
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->orderBy('created_at')
            ->get();

        $demandesEnCours = Approvisionnement::with(['chantier', 'demandeur'])
            ->whereIn('statut', ['validee', 'en_cours_livraison', 'partiellement_recue'])
            ->orderBy('created_at')
            ->get();

        $historique = Approvisionnement::with(['chantier', 'demandeur'])
            ->whereIn('statut', ['rejetee', 'cloturee'])
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();

        return view(
            'direction.appro.index',
            compact('demandesEnAttente', 'demandesEnCours', 'historique')
        );
    }

    // Valider une demande
    public function valider(Approvisionnement $demande)
    {
        $this->approService->valider($demande);
        return back()->with('success', 'Demande validée avec succès.');
    }

    // Rejeter une demande
    public function rejeter(Approvisionnement $demande)
    {
        $this->approService->rejeter($demande);
        return back()->with('success', 'Demande rejetée.');
    }

    // Passer commande
    public function passerCommande(Approvisionnement $demande)
    {
        $this->approService->passerCommande($demande);
        return back()->with('success', 'Commande passée — en cours de livraison.');
    }

    // Historique direction avec filtre dates
    public function historique()
    {
        $dateDebut = request(
            'date_debut',
            now()->startOfMonth()->toDateString()
        );
        $dateFin   = request(
            'date_fin',
            now()->toDateString()
        );

        $demandes = Approvisionnement::with(['chantier', 'demandeur', 'rapportsEntrees'])
            ->whereIn('statut', ['cloturee', 'rejetee'])
            ->whereBetween('updated_at', [
                $dateDebut . ' 00:00:00',
                $dateFin   . ' 23:59:59',
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy('statut');

        // Stats
        $stats = [
            'cloturees' => $demandes->get('cloturee', collect())->count(),
            'rejetees'  => $demandes->get('rejetee',  collect())->count(),
            'total'     => $demandes->flatten()->count(),
        ];

        return view(
            'direction.appro.historique',
            compact('demandes', 'stats', 'dateDebut', 'dateFin')
        );
    }

    // ══════════════════════════════════════════════════════════
    // POINTEUR — Réceptions
    // ══════════════════════════════════════════════════════════

    // Liste des livraisons en cours pour le chantier du pointeur
    public function livraisons()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $livraisons = Approvisionnement::with(['demandeur', 'rapportsEntrees'])
            ->where('chantier_id', $chantier->id)
            ->whereIn('statut', ['en_cours_livraison', 'partiellement_recue'])
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->get();

        return view(
            'pointeur.appro.livraisons',
            compact('chantier', 'livraisons')
        );
    }

    // Valider une réception
    public function validerReception(ReceptionRequest $request, Approvisionnement $demande)
    {
        try {
            $rapport = $this->approService->validerReception(
                $demande,
                $request->validated(),
                auth()->id()
            );

            return redirect()
                ->route('pointeur.appro.livraisons')
                ->with('success', 'Réception enregistrée. Bon d\'entrée généré.')
                ->with('rapport_id', $rapport->id);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Télécharger le bon d'entrée PDF
    public function bonEntreePdf(RapportsEntree $rapport)
    {
        return $this->approService->genererBonEntree($rapport);
    }

    // Historique des bons d'entrée
    public function historiqueLivraisons()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $dateDebut = request(
            'date_debut',
            now()->startOfMonth()->toDateString()
        );
        $dateFin   = request(
            'date_fin',
            now()->toDateString()
        );

        $bonsEntree = RapportsEntree::with(['demande', 'receptionneePar'])
            ->where('chantier_id', $chantier->id)
            ->whereBetween('date_reception', [$dateDebut, $dateFin])
            ->orderByDesc('date_reception')
            ->get();

        $stats = [
            'total'         => $bonsEntree->count(),
            'completes'     => $bonsEntree->filter(fn($b) => $b->quantite_restante <= 0)->count(),
            'partielles'    => $bonsEntree->filter(fn($b) => $b->quantite_restante > 0)->count(),
        ];

        return view(
            'pointeur.appro.historique',
            compact('chantier', 'bonsEntree', 'stats', 'dateDebut', 'dateFin')
        );
    }
}
