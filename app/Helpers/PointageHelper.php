<?php

namespace App\Helpers;

use App\Models\Ouvrier;
use App\Models\Pointage;
use App\Models\Poste;
use App\Models\RecapHebdomadaire;
use App\Models\TauxSalaire;
use Carbon\Carbon;

class PointageHelper
{
    public static function personnelActif(int $chantierId): \Illuminate\Support\Collection
    {
        return Ouvrier::with('poste')
            ->join('postes', 'ouvriers.poste_id', '=', 'postes.id')
            ->where('ouvriers.chantier_id', $chantierId)
            ->where('ouvriers.statutOuvrier', 'actif')
            ->orderByRaw("TRIM(REPLACE(LOWER(postes.libelle), 'chef ', ''))")
            ->orderByRaw("CASE WHEN LOWER(postes.libelle) LIKE 'chef %' THEN 0 ELSE 1 END")
            ->orderBy('ouvriers.nomOuvrier')
            ->select('ouvriers.*')
            ->get();
    }

    public static function paginer(int $total, int $page, int $parPage): array
    {
        $pages = max(1, (int) ceil($total / $parPage));
        $page  = max(1, min($page, $pages));

        return [
            'page'    => $page,
            'parPage' => $parPage,
            'total'   => $total,
            'pages'   => $pages,
            'debut'   => ($page - 1) * $parPage + 1,
            'fin'     => min($page * $parPage, $total),
        ];
    }

    /**
     * Récupère TOUS les pointages de la semaine pour un chantier en UNE
     * seule requête, groupés par ouvrier. À utiliser systématiquement dès
     * qu'on doit traiter plusieurs ouvriers pour la même semaine (récap,
     * aperçu, totaux) : évite une requête par ouvrier.
     */
    public static function pointagesSemaine(int $chantierId, int $semaine, int $annee): \Illuminate\Support\Collection
    {
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        return Pointage::with('poste')
            ->where('chantier_id', $chantierId)
            ->whereBetween('date', [$samedi->toDateString(), $vendredi->toDateString()])
            ->get()
            ->groupBy('ouvrier_id');
    }

    public static function recapsSemaine(int $chantierId, int $semaine, int $annee): \Illuminate\Support\Collection
    {
        return RecapHebdomadaire::where('chantier_id', $chantierId)
            ->where('semaine', $semaine)
            ->where('annee', $annee)
            ->get()
            ->keyBy('ouvrier_id');
    }

    /**
     * Détermine le poste et le taux à appliquer à un pointage AU MOMENT où
     * il est enregistré. Le résultat est destiné à être figé (snapshot)
     * dans les colonnes poste_id / taux_journalier / taux_heure_sup de la
     * table pointages : une fois écrit, il ne doit plus jamais être
     * recalculé, même si l'ouvrier change de poste ou si le taux du poste
     * est reconfiguré par la suite.
     */
    public static function snapshotTaux(Ouvrier $ouvrier, int $chantierId): array
    {
        $taux = TauxSalaire::where('poste_id', $ouvrier->poste_id)
            ->where('chantier_id', $chantierId)
            ->first();

        return [
            'poste_id'        => $ouvrier->poste_id,
            'taux_journalier' => $taux->taux_journalier ?? null,
            'taux_heure_sup'  => $taux->taux_heure_sup ?? null,
        ];
    }

    /**
     * Détermine quel poste afficher pour un ouvrier sur une semaine donnée :
     * celui enregistré (figé) sur ses pointages de la semaine, jamais le
     * poste actuel de l'ouvrier — qui a pu changer depuis. On prend le
     * premier poste renseigné trouvé parmi les pointages de la semaine
     * (par ordre de date) ; si l'ouvrier n'a encore aucun pointage saisi
     * cette semaine (nouvelle semaine pas encore pointée), on retombe sur
     * son poste actuel à titre indicatif uniquement.
     *
     * @param \Illuminate\Support\Collection $pointagesOuvrier Pointages de
     *   l'ouvrier pour la semaine, keyBy date (comme construit dans
     *   construireLigneOuvrier() et SalaireController).
     */
    public static function posteDepuisPointages(Ouvrier $ouvrier, \Illuminate\Support\Collection $pointagesOuvrier): ?Poste
    {
        $poste = $pointagesOuvrier
            ->sortKeys()
            ->pluck('poste')
            ->filter()
            ->first();

        return $poste ?: $ouvrier->poste;
    }

    /**
     * Calcule le salaire à partir d'une collection de pointages DÉJÀ
     * CHARGÉE (peu importe leur ordre). Fonction pure, sans aucune requête
     * DB : c'est le chemin à privilégier partout où la collection de
     * pointages de la semaine est déjà disponible (boucle sur plusieurs
     * ouvriers), pour ne jamais refaire une requête par ouvrier.
     *
     * Le taux utilisé est celui figé (snapshot) sur CHAQUE pointage au
     * moment de sa saisie, jamais la configuration TauxSalaire actuelle ni
     * le poste actuel de l'ouvrier.
     */
    public static function calculerSalaireDepuisPointages(\Illuminate\Support\Collection $pointages): array
    {
        $presents = $pointages->where('statutPointage', 'present');

        $joursPresents  = $presents->count();
        $totalHeuresSup = (int) $pointages->sum('heures_sup');

        $salaireBase      = (float) $presents->sum(fn($p) => (float) ($p->taux_journalier ?? 0));
        $salaireHeuresSup = (float) $pointages->sum(
            fn($p) => (int) $p->heures_sup * (float) ($p->taux_heure_sup ?? 0)
        );

        return [
            'jours_presents'     => $joursPresents,
            'total_heures_sup'   => $totalHeuresSup,
            'salaire_base'       => $salaireBase,
            'salaire_heures_sup' => $salaireHeuresSup,
            'salaire_total'      => $salaireBase + $salaireHeuresSup,
        ];
    }

    /**
     * Variante pratique pour un calcul isolé (un seul ouvrier, hors
     * boucle) : va chercher elle-même les pointages en base. Si vous
     * traitez plusieurs ouvriers pour la même semaine, préférez récupérer
     * la collection une fois via pointagesSemaine() puis appeler
     * calculerSalaireDepuisPointages() directement, pour éviter une
     * requête par ouvrier.
     */
    public static function calculerSalaireOuvrier(Ouvrier $ouvrier, int $chantierId, int $semaine, int $annee): array
    {
        $samedi   = SemaineHelper::debutDepuisNumero($semaine, $annee);
        $vendredi = SemaineHelper::finDepuisNumero($semaine, $annee);

        $pointages = Pointage::where('ouvrier_id', $ouvrier->id)
            ->where('chantier_id', $chantierId)
            ->whereBetween('date', [$samedi->toDateString(), $vendredi->toDateString()])
            ->get();

        return self::calculerSalaireDepuisPointages($pointages);
    }

    /**
     * Construit la ligne d'affichage d'un ouvrier pour une semaine donnée.
     *
     * $pointages doit déjà contenir TOUS les pointages du chantier pour la
     * semaine, groupés par ouvrier_id (issu de pointagesSemaine()) : cette
     * fonction ne fait plus AUCUNE requête DB, tout est calculé en mémoire
     * à partir de ce qui a été chargé une seule fois pour tout le chantier.
     */
    public static function construireLigneOuvrier(
        Ouvrier $ouvrier,
        array $jours,
        \Illuminate\Support\Collection $pointages,
        int $chantierId,
        int $semaine,
        int $annee
    ): array {
        $pointagesOuvrier = $pointages
            ->get($ouvrier->id, collect())
            ->keyBy(fn($p) => Carbon::parse($p->date)->toDateString());

        $joursDetails = collect($jours)->map(fn($jour) => [
            'date'   => $jour,
            'statut' => $pointagesOuvrier->get($jour->toDateString())?->statutPointage ?? null,
            'h_sup'  => (int) ($pointagesOuvrier->get($jour->toDateString())?->heures_sup ?? 0),
            // Poste enregistré CE JOUR-LÀ lors du pointage (peut différer
            // du poste actuel de l'ouvrier s'il a été réaffecté depuis).
            'poste'  => $pointagesOuvrier->get($jour->toDateString())?->poste,
        ]);

        $salaire = self::calculerSalaireDepuisPointages($pointagesOuvrier->values());

        return [
            'ouvrier'       => $ouvrier,
            // Poste à afficher pour cette ligne : celui figé sur les
            // pointages de la semaine, jamais le poste actuel de l'ouvrier.
            'poste'         => self::posteDepuisPointages($ouvrier, $pointagesOuvrier),
            'jours'         => $joursDetails,
            'jours_present' => $joursDetails->where('statut', 'present')->count(),
            'total_h_sup'   => $joursDetails->sum('h_sup'),
            'salaire_base'  => $salaire['salaire_base'],
            'salaire_h_sup' => $salaire['salaire_heures_sup'],
            'salaire_total' => $salaire['salaire_total'],
        ];
    }
}
