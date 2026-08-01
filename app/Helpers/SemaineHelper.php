<?php

namespace App\Helpers;

use Carbon\Carbon;

class SemaineHelper
{
    /**
     * Retourne le Samedi de début du cycle auquel appartient $date.
     *
     * Logique :
     *  - Sam (6) → c'est lui-même
     *  - Dim (0) → hier (Sam)
     *  - Lun (1) → il y a 2 jours (Sam)
     *  - Mar (2) → il y a 3 jours (Sam)
     *  - Mer (3) → il y a 4 jours (Sam)
     *  - Jeu (4) → il y a 5 jours (Sam)
     *  - Ven (5) → il y a 6 jours (Sam)
     */
    public static function debutCycle(Carbon $date): Carbon
    {
        $dow = $date->dayOfWeek; // 0=Dim, 1=Lun, ..., 5=Ven, 6=Sam

        $reculJours = match ($dow) {
            Carbon::SATURDAY => 0, // Sam → lui-même
            Carbon::SUNDAY   => 1, // Dim → Sam d'hier
            Carbon::MONDAY   => 2,
            Carbon::TUESDAY  => 3,
            Carbon::WEDNESDAY => 4,
            Carbon::THURSDAY => 5,
            Carbon::FRIDAY   => 6, // Ven → Sam il y a 6 jours
            default          => 0,
        };

        return $date->copy()->subDays($reculJours)->startOfDay();
    }

    /**
     * Retourne le Vendredi de fin du cycle.
     */
    public static function finCycle(Carbon $date): Carbon
    {
        return self::debutCycle($date)->addDays(6)->endOfDay();
    }

    /**
     * Numéro de semaine du cycle = isoWeek() du Samedi de début.
     */
    public static function numeroCycle(Carbon $date): int
    {
        return self::debutCycle($date)->isoWeek();
    }

    /**
     * Année du cycle = année du Samedi de début.
     */
    public static function anneeCycle(Carbon $date): int
    {
        return self::debutCycle($date)->year;
    }

    /**
     * Samedi de début à partir d'un numéro de semaine et d'année.
     *
     * On cherche le Samedi dont isoWeek() = $semaine et year = $annee.
     * Méthode : partir du Lundi de la semaine ISO et reculer de 2 jours.
     * Mais attention : le Lundi de la semaine ISO peut être dans l'année suivante.
     *
     * On utilise setISODate() qui est fiable.
     */
    public static function debutDepuisNumero(int $semaine, int $annee): Carbon
    {
        // Lundi de la semaine ISO $semaine / $annee
        $lundi = Carbon::now()
            ->setISODate($annee, $semaine, 1) // 1 = Lundi dans ISO
            ->startOfDay();

        // Samedi précédent = Lundi - 2 jours
        $samedi = $lundi->subDays(2);

        // Vérification : le Samedi doit bien avoir isoWeek() == $semaine
        // Si ce n'est pas le cas (cas limite fin d'année), on corrige
        if ($samedi->isoWeek() !== $semaine) {
            // Chercher le bon Samedi par force brute sur ±7 jours
            for ($i = -7; $i <= 7; $i++) {
                $candidat = $samedi->copy()->addDays($i);
                if (
                    $candidat->dayOfWeek === Carbon::SATURDAY
                    && $candidat->isoWeek() === $semaine
                    && $candidat->year === $annee
                ) {
                    return $candidat->startOfDay();
                }
            }
        }

        return $samedi->startOfDay();
    }

    /**
     * Vendredi de fin à partir d'un numéro de semaine et d'année.
     */
    public static function finDepuisNumero(int $semaine, int $annee): Carbon
    {
        return self::debutDepuisNumero($semaine, $annee)
            ->addDays(6)
            ->endOfDay();
    }

    /**
     * Les 7 jours du cycle Sam → Ven.
     */
    public static function jours(int $semaine, int $annee): array
    {
        $samedi = self::debutDepuisNumero($semaine, $annee);
        $jours  = [];
        for ($i = 0; $i <= 6; $i++) {
            $jours[] = $samedi->copy()->addDays($i)->startOfDay();
        }
        return $jours;
    }

    /**
     * Libellé lisible : "Semaine 32 — 1 août au 7 août 2026"
     */
    public static function libelle(int $semaine, int $annee): string
    {
        $sam = self::debutDepuisNumero($semaine, $annee);
        $ven = self::finDepuisNumero($semaine, $annee);

        return 'Semaine ' . $semaine
            . ' — ' . $sam->locale('fr')->isoFormat('D MMM')
            . ' au ' . $ven->locale('fr')->isoFormat('D MMM YYYY');
    }

    /**
     * Vérifie qu'une date appartient bien à un cycle donné.
     */
    public static function appartientAuCycle(
        Carbon $date,
        int $semaine,
        int $annee
    ): bool {
        return self::numeroCycle($date) === $semaine
            && self::anneeCycle($date) === $annee;
    }
}
