<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ATTENTION : si des doublons existent déjà en base (double soumission,
     * ancien bug, etc.), cette migration échouera. Nettoyez-les avant de la
     * lancer, par exemple :
     *   SELECT ouvrier_id, chantier_id, date, COUNT(*) FROM pointages
     *   GROUP BY ouvrier_id, chantier_id, date HAVING COUNT(*) > 1;
     */
    public function up(): void
    {
        Schema::table('pointages', function (Blueprint $table) {
            // Un seul pointage par ouvrier/chantier/jour : empêche un double
            // submit ou une requête concurrente de créer deux lignes.
            $table->unique(['ouvrier_id', 'chantier_id', 'date'], 'pointages_ouvrier_chantier_date_unique');

            // Accélère les requêtes filtrant par chantier + plage de dates
            // (PointageHelper::pointagesSemaine, getPointagesDuJour, ...).
            $table->index(['chantier_id', 'date'], 'pointages_chantier_date_index');
        });

        Schema::table('taux_salaires', function (Blueprint $table) {
            // Un seul taux actif par poste et par chantier : évite qu'une
            // requête ambiguë (::first()) ne prenne un taux au hasard.
            $table->unique(['poste_id', 'chantier_id'], 'taux_salaires_poste_chantier_unique');
        });

        Schema::table('recaps_hebdomadaires', function (Blueprint $table) {
            // Un seul récap par ouvrier/chantier/semaine/année.
            $table->unique(
                ['ouvrier_id', 'chantier_id', 'semaine', 'annee'],
                'recaps_ouvrier_chantier_semaine_annee_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pointages', function (Blueprint $table) {
            $table->dropUnique('pointages_ouvrier_chantier_date_unique');
            $table->dropIndex('pointages_chantier_date_index');
        });

        Schema::table('taux_salaires', function (Blueprint $table) {
            $table->dropUnique('taux_salaires_poste_chantier_unique');
        });

        Schema::table('recaps_hebdomadaires', function (Blueprint $table) {
            $table->dropUnique('recaps_ouvrier_chantier_semaine_annee_unique');
        });
    }
};
