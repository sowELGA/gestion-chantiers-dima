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
        Schema::create('phases', function (Blueprint $table) {
            $table->id();
            $table->string('nomPhase');
            $table->integer('ordre')->default(1);
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->integer('avancement')->default(0);
            $table->enum('statutPhase', [
                'en_attente',
                'en_cours',
                'terminee'
            ])->default('en_attente');
            $table->foreignId('chantier_id')
                ->constrained('chantiers', 'id')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};
