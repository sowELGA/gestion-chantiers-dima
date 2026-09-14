<?php

namespace App\Http\Controllers\Pointeur;

use App\Helpers\ApprovisionnementHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Approvisionnement\ReceptionRequest;
use App\Models\Approvisionnement;
use App\Models\Chantier;
use App\Models\RapportEntree;
use App\Services\ApprovisionnementService;

class ReceptionController extends Controller
{
    public function __construct(
        protected ApprovisionnementService $approService
    ) {}

    public function livraisons()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $livraisons = Approvisionnement::with(['demandeur', 'rapportsEntrees'])
            ->where('chantier_id', $chantier->id)
            ->whereIn('statutAppro', ['en_cours_livraison', 'partiellement_recue'])
            ->orderByRaw("FIELD(priorite, 'urgent', 'normal')")
            ->get();

        return view(
            'pointeur.appro.livraisons',
            compact('chantier', 'livraisons')
        );
    }

    public function validerReception(ReceptionRequest $request, Approvisionnement $demande)
    {
        // La vérification d'appartenance du chantier au pointeur connecté
        // est faite dans ReceptionRequest::authorize() — un pointeur qui
        // n'est pas responsable du chantier de cette demande reçoit déjà
        // un 403 avant d'atteindre cette méthode.
        try {
            $rapport = $this->approService->receptionner(
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

    public function bonEntreePdf(RapportEntree $rapport)
    {
        // Vérification manquante auparavant : n'importe quel pointeur
        // authentifié pouvait télécharger le bon d'entrée de n'importe
        // quel autre chantier, en devinant/énumérant l'ID du rapport.
        $chantier = Chantier::where('pointeur_id', auth()->id())->first();
        abort_if(!$chantier || $rapport->chantier_id !== $chantier->id, 403);

        return $this->approService->genererBonEntreePdf($rapport);
    }

    public function historiqueLivraisons()
    {
        $chantier = Chantier::where('pointeur_id', auth()->id())->firstOrFail();

        $filtres = request()->validate([
            'date_debut' => 'nullable|date',
            'date_fin'   => 'nullable|date|after_or_equal:date_debut',
        ]);

        $dateDebut = $filtres['date_debut'] ?? now()->startOfMonth()->toDateString();
        $dateFin   = $filtres['date_fin'] ?? now()->toDateString();

        $bonsEntree = RapportEntree::with(['demande.rapportsEntrees', 'receptionneePar'])
            ->where('chantier_id', $chantier->id)
            ->whereBetween('date_reception', [$dateDebut, $dateFin])
            ->orderByDesc('date_reception')
            ->get();

        $stats = [
            'total'     => $bonsEntree->count(),
            'completes'  => $bonsEntree->filter(fn($b) => ApprovisionnementHelper::quantiteRestante($b->demande) <= 0)->count(),
            'partielles' => $bonsEntree->filter(fn($b) => ApprovisionnementHelper::quantiteRestante($b->demande) > 0)->count(),
        ];

        return view(
            'pointeur.appro.historique',
            compact('chantier', 'bonsEntree', 'stats', 'dateDebut', 'dateFin')
        );
    }
}
