<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('manuel_id')->constrained('manuels')->cascadeOnDelete();
            $table->timestamp('date_ouverture')->useCurrent();
            $table->unsignedInteger('duree_secondes')->default(0);
            $table->unsignedInteger('derniere_page')->nullable();
            $table->timestamps();

            $table->index(['manuel_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
