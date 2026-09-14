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
        Schema::create('chantiers', function (Blueprint $table) {
            $table->id();
            $table->string('nomChantier');
            $table->string('localisation');
            $table->decimal('budget_prevu', 15, 2)->nullable();
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->date('date_fin_reelle')->nullable();
            $table->enum('statut', [
                'en_attente',
                'en_cours',
                'suspendu',
                'livre'
            ])->default('en_attente');
            $table->foreignId('chef_projet_id')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('restrict');
            $table->foreignId('pointeur_id')
                ->nullable()
                ->constrained('users', 'id')
                ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('user_chantier', function (Blueprint $table) {
            $table->id();
            $table->date('debut_affectation');
            $table->date('fin_affectation')->nullable();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('chantier_id')
                ->constrained('chantiers')
                ->onDelete('cascade');
            $table->timestamps();

            $table->index(['chantier_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantiers');
        Schema::dropIfExists('user_chantier');
    }
};
