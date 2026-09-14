<?php

namespace App\Helpers;

use App\Models\Approvisionnement;
use Carbon\Carbon;

class ApprovisionnementHelper
{
    /**
     * Calcule la priorité en fonction de la date de livraison souhaitée.
     *
     * "Urgent" = la date est aujourd'hui, dans les 2 prochains jours, ou
     * déjà dépassée (une échéance manquée mérite d'être encore plus
     * visible, pas moins). On travaille en jours calendaires entiers
     * (via startOfDay()/today()) plutôt qu'en diff d'heures signée :
     * la colonne est une DATE pure (pas de composante horaire), et
     * diffInHours(..., false) dépend d'une convention de signe Carbon
     * facile à inverser par erreur. Avec l'ancien code, une livraison
     * prévue AUJOURD'HUI n'était jamais "urgent" (isFuture() est déjà
     * faux à partir de 00h00 le jour même), alors qu'une livraison
     * demain l'était — inversé par rapport à l'intuition métier.
     */
    public static function calculerPriorite(string $dateLivraison): string
    {
        $date       = Carbon::parse($dateLivraison)->startOfDay();
        $aujourdHui = today();

        if ($date->lte($aujourdHui)) {
            return 'urgent';
        }

        return $aujourdHui->diffInDays($date) <= 2 ? 'urgent' : 'normal';
    }

    /**
     * Calcule le total cumulé des réceptions pour une demande.
     *
     * Réutilise la relation rapportsEntrees si elle est déjà chargée
     * (eager loading) au lieu de relancer systématiquement une requête —
     * important car cette méthode est appelée en boucle sur des listes de
     * demandes (ex: ReceptionController::historiqueLivraisons()).
     */
    public static function totalRecu(Approvisionnement $demande): float
    {
        $rapports = $demande->relationLoaded('rapportsEntrees')
            ? $demande->rapportsEntrees
            : $demande->rapportsEntrees()->get();

        return (float) $rapports->sum('quantite_recue');
    }

    /**
     * Calcule la quantité restant à livrer.
     */
    public static function quantiteRestante(Approvisionnement $demande): float
    {
        return max(0, (float) ($demande->quantite_demandee - self::totalRecu($demande)));
    }

    /**
     * Détermine le statut après une réception.
     */
    public static function determinerStatutApresReception(Approvisionnement $demande): string
    {
        return self::totalRecu($demande) >= $demande->quantite_demandee
            ? 'cloturee'
            : 'partiellement_recue';
    }
}
