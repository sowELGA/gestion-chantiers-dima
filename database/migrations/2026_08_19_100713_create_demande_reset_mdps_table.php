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
        Schema::create('demandes_reset_mdp', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->enum('statut', ['en_attente', 'traitee'])->default('en_attente');
            $table->timestamp('traite_le')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_reset_mdps');
    }
};
