<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('disponibilites', function (Blueprint $col) {
        $col->id();
        $col->foreignId('medecin_id')->constrained()->onDelete('cascade');
        $col->date('date_travail'); // Le jour coché
        $col->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilites');
    }
};
