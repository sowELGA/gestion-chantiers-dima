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
            $table->string('adresse');
            $table->decimal('budget_prevu', 15, 2);
            $table->decimal('budget_consomme', 15, 2)->default(0);
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantiers');
    }
};
