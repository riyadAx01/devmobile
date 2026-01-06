<?php
// Example Laravel Migration File
// File: database/migrations/YYYY_MM_DD_create_reservations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caftan_id')->constrained()->onDelete('cascade')->index();
            $table->foreignId('client_id')->constrained()->onDelete('cascade')->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Composite index for availability checks
            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
