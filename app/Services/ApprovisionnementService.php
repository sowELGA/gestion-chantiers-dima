<?php

namespace App\Services;

use App\Models\Approvisionnement;
use App\Models\RapportsEntree;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ApprovisionnementService
{
    // ══════════════════════════════════════════════════════════
    // DEMANDES — CRUD
    // ══════════════════════════════════════════════════════════

    public function creer(array $data, int $demandeurId): Approvisionnement
    {
        return Approvisionnement::create([
            'designation'             => $data['designation'],
            'quantite_demandee'       => $data['quantite_demandee'],
            'unite'                   => $data['unite'],
            'priorite'                => $this->calculerPriorite(
                $data['date_livraison_souhaitee'] ?? null
            ),
            'statut'                  => 'en_attente',
            'date_livraison_souhaitee' => $data['date_livraison_souhaitee'] ?? null,
            'chantier_id'             => $data['chantier_id'],
            'demandeur_id'            => $demandeurId,
        ]);
    }

    public function modifier(Approvisionnement $appro, array $data): Approvisionnement
    {
        $this->verifierModifiable($appro);

        $appro->update([
            'designation'             => $data['designation'],
            'quantite_demandee'       => $data['quantite_demandee'],
            'unite'                   => $data['unite'],
            'priorite'                => $this->calculerPriorite(
                $data['date_livraison_souhaitee']
            ),
            'date_livraison_souhaitee' => $data['date_livraison_souhaitee'],
        ]);

        return $appro->fresh();
    }

    public function supprimer(Approvisionnement $appro): void
    {
        $this->verifierSupprimable($appro);
        $appro->delete();
    }

    // ══════════════════════════════════════════════════════════
    // TRAITEMENT — DIRECTION
    // ══════════════════════════════════════════════════════════

    public function valider(Approvisionnement $appro): Approvisionnement
    {
        $this->verifierStatut($appro, 'en_attente', 'valider');

        $appro->update(['statut' => 'validee']);

        return $appro->fresh();
    }

    public function rejeter(Approvisionnement $appro): Approvisionnement
    {
        $this->verifierStatut($appro, 'en_attente', 'rejeter');

        $appro->update(['statut' => 'rejetee']);

        return $appro->fresh();
    }

    public function commander(Approvisionnement $appro): Approvisionnement
    {
        $this->verifierStatut($appro, 'validee', 'commander');

        $appro->update([
            'statut'                => 'en_cours_livraison',
            'date_commande'         => now()->toDateString(),
        ]);

        return $appro->fresh();
    }

    // ══════════════════════════════════════════════════════════
    // RÉCEPTION — POINTEUR
    // ══════════════════════════════════════════════════════════

    public function receptionner(
        Approvisionnement $appro,
        array $data,
        int $pointeurId
    ): RapportsEntree {
        $this->verifierReceptionnable($appro);

        $rapport = $this->enregistrerReception($appro, $data, $pointeurId);

        $this->mettreAJourStatutApres($appro, $rapport);

        return $rapport;
    }

    public function genererBonEntreePdf(RapportsEntree $rapport)
    {
        $pdf = Pdf::loadView('pdf.bon-entree', [
            'rapport'  => $rapport->load(['demande.chantier', 'receptionneeParUser']),
        ])->setPaper('A4', 'portrait');

        $nomFichier = 'bon-entree-'
            . $rapport->id
            . '-' . now()->format('d-m-Y')
            . '.pdf';

        return $pdf->download($nomFichier);
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS — VÉRIFICATIONS
    // ══════════════════════════════════════════════════════════

    private function verifierModifiable(Approvisionnement $appro): void
    {
        if ($appro->statut !== 'en_attente') {
            throw new \Exception(
                'Impossible de modifier une demande qui n\'est plus en attente.'
            );
        }
    }

    private function verifierSupprimable(Approvisionnement $appro): void
    {
        if (!in_array($appro->statut, ['en_attente', 'validee'])) {
            throw new \Exception(
                'Impossible de supprimer une demande en cours de livraison ou clôturée.'
            );
        }
    }

    private function verifierReceptionnable(Approvisionnement $appro): void
    {
        if (!in_array($appro->statut, ['en_cours_livraison', 'partiellement_recue'])) {
            throw new \Exception(
                'Cette demande ne peut pas être réceptionnée.'
            );
        }
    }

    private function verifierStatut(
        Approvisionnement $appro,
        string $statutAttendu,
        string $action
    ): void {
        if ($appro->statut !== $statutAttendu) {
            throw new \Exception(
                "Impossible de {$action} une demande au statut '{$appro->statut}'."
            );
        }
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVÉS — ACTIONS
    // ══════════════════════════════════════════════════════════

    // Calcule la priorité selon la date souhaitée
    private function calculerPriorite(string $dateLivraison): string
    {
        $date = Carbon::parse($dateLivraison);

        return now()->diffInHours($date, false) <= 24
            && $date->isFuture()
            ? 'urgent'
            : 'normal';
    }

    // Enregistre le rapport d'entrée
    private function enregistrerReception(
        Approvisionnement $appro,
        array $data,
        int $pointeurId
    ): RapportsEntree {
        $totalDejaRecu = $appro->rapportsEntrees()->sum('quantite_recue');
        $quantiteRecue = (float) $data['quantite_recue'];
        $totalApres    = $totalDejaRecu + $quantiteRecue;
        $restante      = max(0, $appro->quantite_demandee - $totalApres);

        return RapportsEntree::create([
            'demande_id'            => $appro->id,
            'chantier_id'           => $appro->chantier_id,
            'receptionnee_par_id'   => $pointeurId,
            'quantite_commandee'    => $appro->quantite_demandee,
            'quantite_totale_recue' => $totalApres,
            'quantite_recue'        => $quantiteRecue,
            'quantite_restante'     => $restante,
            'date_reception'        => now()->toDateString(),
            'observation'           => $data['observation'] ?? null,
        ]);
    }

    // Met à jour le statut de la demande après réception
    private function mettreAJourStatutApres(
        Approvisionnement $appro,
        RapportsEntree $rapport
    ): void {
        $statut = $rapport->quantite_restante <= 0
            ? 'cloturee'
            : 'partiellement_recue';

        $appro->update(['statut' => $statut]);
    }
}
