<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compte_emetteur_id')->constrained('comptes')->cascadeOnDelete();
            $table->foreignId('compte_destinataire_id')->constrained('comptes')->cascadeOnDelete();
            $table->decimal('montant', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virements');
    }
};
