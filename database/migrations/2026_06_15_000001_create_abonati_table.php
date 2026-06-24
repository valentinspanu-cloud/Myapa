<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonati', function (Blueprint $table) {
            $table->id();

            // Identificatori Oracle
            $table->string('cod_abonat')->unique()->index();
            $table->unsignedBigInteger('id_client')->index();
            $table->unsignedBigInteger('id_locatie')->nullable();

            // Date abonat
            $table->string('nume_abonat');
            $table->string('adresa')->nullable();
            $table->string('localitate')->nullable();
            $table->string('strada')->nullable();
            $table->string('nr_strada')->nullable();
            $table->string('bloc')->nullable();
            $table->string('addr_stair')->nullable();
            $table->string('addr_apt')->nullable();

            // Contact & contract
            $table->string('telefon')->nullable();
            $table->string('nr_contract')->nullable();

            // Ruta
            $table->string('ruta')->nullable()->index();
            $table->string('sector')->nullable();

            // Sync
            $table->timestamp('sincronizat_la')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonati');
    }
};
