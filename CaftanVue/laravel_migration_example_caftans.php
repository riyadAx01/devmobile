<?php
// Example Laravel Migration File
// File: database/migrations/YYYY_MM_DD_create_caftans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caftans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Indexed for search
            $table->text('description');
            $table->string('image_url', 500);
            $table->decimal('price', 10, 2);
            $table->enum('collection', ['Traditional', 'Modern', 'Wedding', 'Casual'])->index();
            $table->string('color', 50)->index();
            $table->enum('size', ['S', 'M', 'L', 'XL']);
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available')->index();
            $table->timestamps();

            // Composite index for common queries
            $table->index(['collection', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caftans');
    }
};
