<?php

namespace App\Services;

use App\Models\Approvisionnement;
use App\Models\RapportsEntree;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ApprovisionnementService
{
    // ── CHEF DE PROJET ────────────────────────────────────────

    public function creer(array $data, int $demandeurId): Approvisionnement
    {
        return Approvisionnement::create([
            'designation'          => $data['designation'],
            'quantite_demandee'    => $data['quantite_demandee'],
            'unite'                => $data['unite'],
            'priorite'             => (Carbon::parse($data['date_livraison_souhaitee'])->diffInHours(now()) <= 24
                ? 'urgent'
                : 'normal'),
            'statut'               => 'en_attente',
            'date_livraison_souhaitee' => $data['date_livraison_souhaitee'],
            'chantier_id'          => $data['chantier_id'],
            'demandeur_id'         => $demandeurId,
        ]);
    }


    public function modifier(Approvisionnement $appro, array $data): Approvisionnement
    {
        if ($appro->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de modifier une demande qui n\'est plus en attente.'
            );
        }

        $appro->update([
            'designation'             => $data['designation'],
            'quantite_demandee'       => $data['quantite_demandee'],
            'unite'                   => $data['unite'],
            'priorite'             => (Carbon::parse($data['date_livraison_souhaitee'])->diffInHours(now()) <= 24
                ? 'urgent'
                : 'normal'),
            'date_livraison_souhaitee' => $data['date_livraison_souhaitee'],
        ]);

        return $appro->fresh();
    }

    public function supprimer(Approvisionnement $appro): void
    {
        if (!in_array($appro->statut, ['en_attente', 'validee'])) {
            throw new \Exception(
                'Impossible de supprimer une demande en cours de livraison ou clôturée.'
            );
        }

        $appro->delete();
    }


    // ── DIRECTION ─────────────────────────────────────────────

    public function valider(Approvisionnement $demande): Approvisionnement
    {
        $demande->update(['statut' => 'validee']);
        return $demande;
    }

    public function rejeter(Approvisionnement $demande): Approvisionnement
    {
        $demande->update(['statut' => 'rejetee']);
        return $demande;
    }

    public function passerCommande(Approvisionnement $demande): Approvisionnement
    {
        $demande->update([
            'statut' => 'en_cours_livraison',
            'date_commande' => now()->toDateString(),
        ]);
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
