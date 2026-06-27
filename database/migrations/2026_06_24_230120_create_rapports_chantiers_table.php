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
        Schema::create('rapports_chantiers', function (Blueprint $table) {
            $table->id();
            $table->date('date_rapport');
            $table->text('contenu');
            $table->foreignId('chantier_id')
                ->constrained('chantiers', 'id')
                ->onDelete('cascade');
            $table->foreignId('auteur_id')
                ->constrained('users', 'id')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports_chantiers');
    }
};
