<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Basic, Pro, Entreprise
            $table->string('slug')->unique();             // basic, pro, entreprise
            $table->integer('price_monthly')->default(0); // en XAF
            $table->integer('max_medecins')->nullable();  // null = illimité
            $table->integer('max_rdv_monthly')->nullable();
            $table->boolean('includes_insurance')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
