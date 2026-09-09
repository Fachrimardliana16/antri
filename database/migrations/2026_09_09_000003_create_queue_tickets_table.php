<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number'); // e.g. A-001
            $table->integer('sequence_number'); // 1
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('counter_id')->nullable()->constrained('counters')->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('waiting'); // waiting, calling, serving, completed, skipped, transferred
            $table->string('tracking_token', 64)->unique();
            $table->date('queue_date');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('transferred_to_service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->timestamps();

            $table->index(['service_id', 'status']);
            $table->index(['queue_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_tickets');
    }
};
