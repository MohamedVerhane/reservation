<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('phone', 20);
            $table->string('email');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedTinyInteger('star_rating')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('city');
            $table->index('country');
            $table->index('is_active');
            if ($this->isFullTextSupported()) {
                $table->fullText('name');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }

    private function isFullTextSupported(): bool
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        return ! in_array($connection, ['sqlite', 'sqlite3']);
    }
};
