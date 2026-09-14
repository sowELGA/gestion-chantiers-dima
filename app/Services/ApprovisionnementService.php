<?php

namespace App\Services;

use App\Helpers\ApprovisionnementHelper;
use App\Models\Approvisionnement;
use App\Models\RapportEntree;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ApprovisionnementService
{
    // ══════════════════════════════════════════════════════════
    // CHEF DE PROJET — ACTIONS
    // ══════════════════════════════════════════════════════════

    public function creerPlusieurs(array $data, int $demandeurId): void
    {
        DB::transaction(function () use ($data, $demandeurId) {
            foreach ($data['demandes'] as $item) {
                Approvisionnement::create([
                    'designation'              => $item['designation'],
                    'quantite_demandee'        => $item['quantite_demandee'],
                    'unite'                    => $item['unite'],
                    'date_livraison_souhaitee' => $item['date_livraison_souhaitee'],
                    'priorite'                 => ApprovisionnementHelper::calculerPriorite($item['date_livraison_souhaitee']),
                    'statutAppro'                   => 'en_attente',
                    'chantier_id'              => $data['chantier_id'],
                    'demandeur_id'             => $demandeurId,
                ]);
            }
        });
    }

    /**
     * NB : "chantier_id" est désormais bien pris en compte. Auparavant
     * ce champ était validé par ApprovisionnementRequest mais jamais
     * appliqué ici — la vue d'édition propose pourtant un sélecteur de
     * chantier, donc l'ignorer revenait à un changement silencieusement
     * sans effet malgré un message "Modifié avec succès".
     */
    public function modifier(Approvisionnement $appro, array $data): Approvisionnement
    {
        if ($appro->statutAppro !== 'en_attente') {
            throw new \Exception("Impossible de modifier une demande qui n'est plus en attente.");
        }

        $appro->update([
            'designation'              => $data['designation'],
            'quantite_demandee'        => $data['quantite_demandee'],
            'unite'                    => $data['unite'],
            'priorite'                 => ApprovisionnementHelper::calculerPriorite($data['date_livraison_souhaitee']),
            'date_livraison_souhaitee' => $data['date_livraison_souhaitee'],
            'chantier_id'              => $data['chantier_id'],
        ]);

        return $appro->fresh();
    }

    public function supprimer(Approvisionnement $appro): void
    {
        if (!in_array($appro->statutAppro, ['en_attente', 'validee'])) {
            throw new \Exception("Impossible de supprimer une demande en cours de livraison ou clôturée.");
        }

        $appro->delete();
    }

    // ══════════════════════════════════════════════════════════
    // DIRECTION — ACTIONS
    // ══════════════════════════════════════════════════════════

    public function valider(Approvisionnement $appro): Approvisionnement
    {
        $this->verifierStatut($appro, 'en_attente', 'valider');
        $appro->update(['statutAppro' => 'validee']);

        return $appro->fresh();
    }

    public function rejeter(Approvisionnement $appro): Approvisionnement
    {
        $this->verifierStatut($appro, 'en_attente', 'rejeter');
        $appro->update(['statutAppro' => 'rejetee']);

        return $appro->fresh();
    }

    /**
     * La date de livraison prévue est désormais optionnelle au moment de
     * la commande (renseignée ou non via le pop-up), et peut aussi être
     * laissée null pour être définie/ajustée plus tard via
     * definirDateLivraisonPrevue().
     */
    public function commander(Approvisionnement $appro, ?string $dateLivraisonPrevue = null): Approvisionnement
    {
        $this->verifierStatut($appro, 'validee', 'commander');
        $appro->update([
            'statutAppro'            => 'en_cours_livraison',
            'date_commande'          => now()->toDateString(),
            'date_livraison_prevue'  => $dateLivraisonPrevue,
        ]);

        return $appro->fresh();
    }

    /**
     * Permet à la direction de renseigner ou corriger la date de
     * livraison prévue à tout moment tant que la commande n'est pas
     * clôturée — y compris après l'avoir passée. $date peut être null
     * pour effacer une date précédemment saisie.
     */
    public function definirDateLivraisonPrevue(Approvisionnement $appro, ?string $date): Approvisionnement
    {
        if (!in_array($appro->statutAppro, ['en_cours_livraison', 'partiellement_recue'])) {
            throw new \Exception(
                "La date de livraison prévue ne peut être définie que pour une commande en cours de livraison."
            );
        }

        $appro->update(['date_livraison_prevue' => $date]);

        return $appro->fresh();
    }

    // ══════════════════════════════════════════════════════════
    // POINTEUR — ACTIONS
    // ══════════════════════════════════════════════════════════

    /**
     * Enregistre une réception.
     *
     * IMPORTANT — protection contre la sur-livraison : ReceptionRequest
     * valide déjà la quantité restante, mais ce calcul est fait AVANT le
     * début de la transaction. Si deux réceptions sont soumises presque
     * simultanément pour la même demande, les deux peuvent passer cette
     * validation sur la même quantité restante "vue" au même instant, et
     * la somme des deux dépasser la quantité demandée une fois les deux
     * écritures commitées (race condition classique "check-then-act").
     *
     * On reverrouille donc la ligne ($lockForUpdate) et on revalide la
     * quantité restante À L'INTÉRIEUR de la transaction : la seconde
     * requête concurrente attend que la première commite, puis voit la
     * quantité restante déjà mise à jour et peut être rejetée
     * proprement si elle dépasse désormais le reliquat réel.
     */
    public function receptionner(Approvisionnement $appro, array $data, int $pointeurId): RapportEntree
    {
        if (!in_array($appro->statutAppro, ['en_cours_livraison', 'partiellement_recue'])) {
            throw new \Exception("Cette demande ne peut pas être réceptionnée.");
        }

        return DB::transaction(function () use ($appro, $data, $pointeurId) {
            $appro = Approvisionnement::whereKey($appro->id)->lockForUpdate()->firstOrFail();

            $quantiteRecue    = (float) $data['quantite_recue'];
            $quantiteRestante = ApprovisionnementHelper::quantiteRestante($appro);

            if ($quantiteRecue > $quantiteRestante) {
                throw new \Exception(
                    "La quantité reçue ({$quantiteRecue}) dépasse la quantité restante à livrer ({$quantiteRestante})."
                );
            }

            $rapport = RapportEntree::create([
                'demande_id'          => $appro->id,
                'chantier_id'         => $appro->chantier_id,
                'receptionnee_par_id' => $pointeurId,
                'quantite_recue'      => $quantiteRecue,
                'date_reception'      => now()->toDateString(),
                'observation'         => $data['observation'] ?? null,
            ]);

            $appro->update([
                'statutAppro' => ApprovisionnementHelper::determinerStatutApresReception($appro->fresh()),
            ]);

            return $rapport;
        });
    }

    public function genererBonEntreePdf(RapportEntree $rapport)
    {
        $pdf = Pdf::loadView('pdf.bon-entree', [
            'rapport' => $rapport->load(['demande.chantier', 'receptionneePar']),
        ])->setPaper('A4', 'portrait');

        $nomFichier = 'bon-entree-' . $rapport->id . '-' . now()->format('d-m-Y') . '.pdf';

        return $pdf->download($nomFichier);
    }

    // ══════════════════════════════════════════════════════════
    // HELPER PRIVÉ INTERNE
    // ══════════════════════════════════════════════════════════

    private function verifierStatut(Approvisionnement $appro, string $statutAttendu, string $action): void
    {
        if ($appro->statutAppro !== $statutAttendu) {
            throw new \Exception("Impossible de {$action} une demande au statut '{$appro->statutAppro}'.");
        }
    }
}
