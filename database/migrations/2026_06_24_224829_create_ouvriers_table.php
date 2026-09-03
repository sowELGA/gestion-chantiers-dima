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
        Schema::create('ouvriers', function (Blueprint $table) {
            $table->id();
            $table->string('nomOuvrier');
            $table->string('prenomOuvrier');
            $table->string('telOuvrier');
            $table->enum('statutOuvrier', [
                'actif',
                'inactif'
            ])->default('actif');
            $table->foreignId('poste_id')
                ->constrained('postes', 'id')
                ->onDelete('restrict');
            $table->foreignId('chantier_id')
                ->constrained('chantiers', 'id')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouvriers');
    }
};
