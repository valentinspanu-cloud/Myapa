<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_send_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 50)->unique();
            $table->string('luna', 20);
            $table->integer('total');
            $table->integer('trimise')->default(0);
            $table->integer('esuate')->default(0);
            $table->enum('status', ['in_progress', 'completed', 'failed'])->default('in_progress');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_send_batches');
    }
};
