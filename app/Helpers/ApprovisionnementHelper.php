<?php

namespace App\Helpers;

use App\Models\Approvisionnement;
use Carbon\Carbon;

class ApprovisionnementHelper
{
    /**
     * Calcule la priorité en fonction de la date de livraison souhaitée.
     */
    public static function calculerPriorite(string $dateLivraison): string
    {
        $date = Carbon::parse($dateLivraison);

        return now()->diffInHours($date, false) <= 48 && $date->isFuture()
            ? 'urgent'
            : 'normal';
    }

    /**
     * Calcule le total cumulé des réceptions pour une demande.
     */
    public static function totalRecu(Approvisionnement $demande): float
    {
        return (float) $demande->rapportsEntrees()->sum('quantite_recue');
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