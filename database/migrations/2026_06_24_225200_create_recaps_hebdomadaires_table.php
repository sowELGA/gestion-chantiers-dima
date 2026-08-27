<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recaps_hebdomadaires', function (Blueprint $table) {
            $table->id();
            $table->integer('semaine');
            $table->integer('annee');
            $table->integer('jours_presents')->default(0);
            $table->integer('total_heures_sup')->default(0);
            $table->enum('statutRecap', [
                'en_attente',
                'soumise',
                'validee_cp',
                'rejetee',
                'envoyee_direction'
            ])->default('en_attente');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->foreignId('ouvrier_id')
                ->constrained('ouvriers', 'id')
                ->onDelete('restrict');
            $table->foreignId('chantier_id')
                ->constrained('chantiers', 'id')
                ->onDelete('restrict');
            $table->foreignId('soumis_par_id')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');
            $table->foreignId('valide_par_id')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recaps_hebdomadaires');
    }
};
