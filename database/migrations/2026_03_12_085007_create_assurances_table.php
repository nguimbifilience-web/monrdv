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
        if (!Schema::hasTable('assurances')) {
            Schema::create('assurances', function (Blueprint $table) {
                $table->id();
                $table->string('nom'); // Nom de l'assurance
                $table->string('code')->nullable(); // Code interne (optionnel)
                $table->text('description')->nullable(); // Description
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('assurances');
    }
};