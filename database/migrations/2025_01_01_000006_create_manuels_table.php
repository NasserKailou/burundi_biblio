<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manuels', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('auteur')->nullable();
            $table->unsignedSmallInteger('annee')->nullable();
            $table->foreignId('matiere_id')->constrained('matieres')->restrictOnDelete();
            $table->string('fichier');
            $table->string('couverture')->nullable();
            $table->enum('type', ['pdf', 'epub']);
            $table->boolean('est_commun')->default(false);
            $table->foreignId('uploader_id')->constrained('users')->restrictOnDelete();
            $table->enum('statut', ['publie', 'brouillon'])->default('brouillon');
            $table->timestamps();

            $table->index(['matiere_id', 'statut']);
            $table->index(['est_commun', 'statut']);
            $table->index('uploader_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuels');
    }
};
