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
        Schema::create('rapports_entrees', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantite_commandee', 10, 2);
            $table->decimal('quantite_totale_recue', 10, 2)->default(0);
            $table->decimal('quantite_recue', 10, 2);
            $table->decimal('quantite_restante', 10, 2)->default(0);
            $table->date('date_reception');
            $table->text('observation')->nullable();
            $table->foreignId('demande_id')
                ->constrained('approvisionnements', 'id')
                ->onDelete('restrict');
            $table->foreignId('chantier_id')
                ->constrained('chantiers', 'id')
                ->onDelete('restrict');
            $table->foreignId('receptionnee_par_id')
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
        Schema::dropIfExists('rapports_entrees');
    }
};
