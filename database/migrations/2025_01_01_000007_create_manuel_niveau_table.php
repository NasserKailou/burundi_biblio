<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manuel_niveau', function (Blueprint $table) {
            $table->foreignId('manuel_id')->constrained('manuels')->cascadeOnDelete();
            $table->foreignId('niveau_id')->constrained('niveaux')->cascadeOnDelete();
            $table->primary(['manuel_id', 'niveau_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuel_niveau');
    }
};
