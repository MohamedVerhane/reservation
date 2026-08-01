<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->unsignedSmallInteger('max_guests')->default(2);
            $table->unsignedSmallInteger('max_children')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('hotel_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
