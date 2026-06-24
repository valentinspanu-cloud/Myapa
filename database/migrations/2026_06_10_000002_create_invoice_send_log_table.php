<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_send_log', function (Blueprint $table) {
            $table->id();
            $table->string('cod_client', 20);
            $table->string('nr_factura', 20);
            $table->string('email', 150);
            $table->enum('status', ['trimis', 'eroare']);
            $table->text('eroare')->nullable();
            $table->timestamp('trimis_la')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_send_log');
    }
};
