<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_extern_clients', function (Blueprint $table) {
            $table->id();
            $table->string('cod_client', 20);
            $table->string('contract_nr', 20);
            $table->string('client_id', 20);   // idfirma Oracle (luat automat)
            $table->string('nume', 150);        // luat automat din Oracle
            $table->string('email', 150);
            $table->timestamps();

            $table->unique('cod_client');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_extern_clients');
    }
};
