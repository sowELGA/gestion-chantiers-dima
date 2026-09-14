<?php

namespace Database\Seeders;

use App\Helpers\SemaineHelper;
use App\Models\Chantier;
use App\Models\Ouvrier;
use App\Models\Pointage;
use App\Models\TauxSalaire;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PointageSeeder extends Seeder
{
    public function run(): void
    {
        $chantiers = Chantier::whereIn('nomChantier', ['3M', 'Al Makhtoum'])->get();

        if ($chantiers->isEmpty()) {
            $this->command->error("Les chantiers '3M' et 'Al Makhtoum' doivent exister avant de lancer ce seeder.");
            return;
        }

        // Samedi de début du cycle en cours, selon le helper
        $cycleActuel = SemaineHelper::debutCycle(Carbon::now());

        foreach ($chantiers as $chantier) {
            $ouvriers = Ouvrier::where('chantier_id', $chantier->id)
                ->where('statutOuvrier', 'actif')
                ->get();

            $tauxParPoste = TauxSalaire::where('chantier_id', $chantier->id)
                ->get()
                ->keyBy('poste_id');

            // 4 cycles : les 3 précédents + le cycle en cours
            for ($i = 3; $i >= 0; $i--) {
                $samedi = $cycleActuel->copy()->subWeeks($i);

                foreach (range(0, 6) as $jourOffset) { // Samedi -> Vendredi
                    $date = $samedi->copy()->addDays($jourOffset);

                    if ($date->isSunday()) {
                        continue; // Aucun pointage enregistré le dimanche
                    }

                    // Pas de pointage pour les jours futurs du cycle en cours
                    if ($date->isFuture()) {
                        continue;
                    }

                    foreach ($ouvriers as $ouvrier) {
                        $taux = $tauxParPoste->get($ouvrier->poste_id);

                        $present = rand(1, 100) <= 90;
                        $heuresSup = ($present && rand(1, 100) <= 20) ? rand(1, 3) : 0;

                        Pointage::create([
                            'date'            => $date->toDateString(),
                            'statutPointage'  => $present ? 'present' : 'absent',
                            'heures_sup'      => $heuresSup,
                            'taux_journalier' => $present ? ($taux->taux_journalier ?? null) : null,
                            'taux_heure_sup'  => $heuresSup > 0 ? ($taux->taux_heure_sup ?? null) : null,
                            'poste_id'        => $ouvrier->poste_id,
                            'ouvrier_id'      => $ouvrier->id,
                            'chantier_id'     => $chantier->id,
                        ]);
                    }
                }
            }
        }
    }
}
