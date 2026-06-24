<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citiri_contoare', function (Blueprint $table) {
            $table->id();

            // Cine a citit
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Cititorul de teren (user Laravel cu rol cititor)');

            // Identificatori Oracle
            $table->unsignedBigInteger('id_cit')->index()
                  ->comment('ID citire Oracle — necesar pentru POST confirmare');
            $table->string('cod_abonat')->index();
            $table->unsignedBigInteger('id_client');
            $table->unsignedBigInteger('id_locatie')->nullable();
            $table->string('ruta')->index();

            // Perioada
            $table->unsignedTinyInteger('luna');
            $table->unsignedSmallInteger('an');

            // Contor
            $table->string('serie_contor')->nullable();
            $table->string('cod_contor')->nullable();
            $table->string('tip_contor')->nullable();

            // Indexuri
            $table->unsignedInteger('index_vechi')->nullable()
                  ->comment('Index anterior din Oracle');
            $table->unsignedInteger('index_nou_oracle')->nullable()
                  ->comment('Index nou deja introdus in Oracle (daca exista)');
            $table->unsignedInteger('index_citit')->nullable()
                  ->comment('Index introdus de cititor pe teren');

            // Sold la momentul citirii
            $table->decimal('sold_moment', 10, 2)->nullable()
                  ->comment('Snapshot sold din Oracle la momentul citirii');

            // Foto & GPS
            $table->string('foto_path')->nullable();
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();

            // Status & audit
            $table->enum('status', ['nou', 'confirmat', 'eroare', 'corectat'])
                  ->default('nou')
                  ->index();
            $table->text('observatii')->nullable();
            $table->string('mesaj_oracle')->nullable()
                  ->comment('Raspunsul io_mesaj de la procedura Oracle la confirmare');

            // Confirmare supervisor
            $table->foreignId('confirmat_de')
                  ->nullable()
                  ->constrained('users')
                  ->comment('Supervisorul care a confirmat');
            $table->timestamp('confirmat_la')->nullable();

            $table->timestamps();

            // Index compus pentru interogari frecvente
            $table->index(['luna', 'an', 'ruta']);
            $table->index(['luna', 'an', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citiri_contoare');
    }
};
