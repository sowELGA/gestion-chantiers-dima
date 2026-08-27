<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports_chantiers', function (Blueprint $table) {
            $table->id();
            $table->date('date_rapport');
            $table->string('titre')->nullable();
            $table->enum('type', [
                'avancement',
                'incident',
                'livraison',
                'reunion',
                'autre'
            ])->default('avancement');
            $table->text('contenu');
            $table->unsignedBigInteger('chantier_id');
            $table->foreign('chantier_id')->references('id')->on('chantiers')->onDelete('cascade');
            $table->unsignedBigInteger('auteur_id');
            $table->foreign('auteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports_chantiers');
    }
};
