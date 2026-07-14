<?php

namespace App\Services;

use App\Models\Approvisionnement;
use App\Models\RapportsEntree;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovisionnementService
{
    // ── CHEF DE PROJET ────────────────────────────────────────

    public function creer(array $data, int $demandeurId): Approvisionnement
    {
        return Approvisionnement::create([
            'designation'          => $data['designation'],
            'quantite_demandee'    => $data['quantite_demandee'],
            'unite'                => $data['unite'],
            'priorite'             => $data['priorite'],
            'statut'               => 'en_attente',
            'date_livraison_prevue' => $data['date_livraison_prevue'] ?? null,
            'chantier_id'          => $data['chantier_id'],
            'demandeur_id'         => $demandeurId,
        ]);
    }

    // ── DIRECTION ─────────────────────────────────────────────

    public function valider(Approvisionnement $demande): Approvisionnement
    {
        $demande->update([
            'statut'        => 'validee',
            'date_commande' => now()->toDateString(),
        ]);
        return $demande;
    }

    public function rejeter(Approvisionnement $demande): Approvisionnement
    {
        $demande->update(['statut' => 'rejetee']);
        return $demande;
    }

    public function passerCommande(Approvisionnement $demande): Approvisionnement
    {
        $demande->update(['statut' => 'en_cours_livraison']);
        return $demande;
    }

    // ── POINTEUR — Réception ──────────────────────────────────

    public function validerReception(
        Approvisionnement $demande,
        array $data,
        int $pointeurId
    ): RapportsEntree {

        // Cumul des réceptions précédentes
        $quantiteDejRecue = RapportsEntree::where('demande_id', $demande->id)
            ->sum('quantite_recue');

        $quantiteTotaleRecue = $quantiteDejRecue + $data['quantite_recue'];
        $quantiteRestante    = max(0, $demande->quantite_demandee - $quantiteTotaleRecue);

        // Créer le bon d'entrée
        $rapport = RapportsEntree::create([
            'demande_id'            => $demande->id,
            'chantier_id'           => $demande->chantier_id,
            'receptionnee_par_id'   => $pointeurId,
            'quantite_commandee'    => $demande->quantite_demandee,
            'quantite_totale_recue' => $quantiteTotaleRecue,
            'quantite_recue'        => $data['quantite_recue'],
            'quantite_restante'     => $quantiteRestante,
            'date_reception'        => now()->toDateString(),
            'observation'           => $data['observation'] ?? null,
        ]);

        // Mettre à jour le statut de la demande
        $statut = $quantiteRestante <= 0 ? 'cloturee' : 'partiellement_recue';
        $demande->update(['statut' => $statut]);

        return $rapport;
    }

    // Générer le bon d'entrée PDF
    public function genererBonEntree(RapportsEntree $rapport)
    {
        $rapport->load(['demande.chantier', 'receptionneePar']);

        $pdf = Pdf::loadView('pdf.bon-entree', compact('rapport'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'bon-entree-' . $rapport->id . '-' .
                now()->format('Y-m-d') . '.pdf'
        );
    }
}
