<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('email_verified_at', 'idx_users_email_verified');
        });

        // Reservations table indexes
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('user_id', 'idx_reservations_user_id');
            $table->index('status', 'idx_reservations_status');
            $table->index(['check_in', 'check_out'], 'idx_reservations_dates');
            $table->index(['hotel_id', 'status'], 'idx_reservations_hotel_status');
            $table->index('created_at', 'idx_reservations_created_at');
        });

        // Payments table indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('reservation_id', 'idx_payments_reservation_id');
            $table->index('status', 'idx_payments_status');
            $table->index('method', 'idx_payments_method');
            $table->index('paid_at', 'idx_payments_paid_at');
        });

        // Reviews table indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('reservation_id', 'idx_reviews_reservation_id');
            $table->index(['hotel_id', 'is_approved'], 'idx_reviews_hotel_approved');
            $table->index(['hotel_id', 'rating', 'is_approved'], 'idx_reviews_hotel_rating_approved');
            $table->index('is_approved', 'idx_reviews_approved');
        });

        // Rooms table indexes
        Schema::table('rooms', function (Blueprint $table) {
            $table->index(['hotel_id', 'is_active'], 'idx_rooms_hotel_active');
            $table->index(['hotel_id', 'status'], 'idx_rooms_hotel_status');
            $table->index(['room_type_id', 'is_active'], 'idx_rooms_room_type_active');
            $table->index('status', 'idx_rooms_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_email_verified');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_reservations_user_id');
            $table->dropIndex('idx_reservations_status');
            $table->dropIndex('idx_reservations_dates');
            $table->dropIndex('idx_reservations_hotel_status');
            $table->dropIndex('idx_reservations_created_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_reservation_id');
            $table->dropIndex('idx_payments_status');
            $table->dropIndex('idx_payments_method');
            $table->dropIndex('idx_payments_paid_at');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_reservation_id');
            $table->dropIndex('idx_reviews_hotel_approved');
            $table->dropIndex('idx_reviews_hotel_rating_approved');
            $table->dropIndex('idx_reviews_approved');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_hotel_active');
            $table->dropIndex('idx_rooms_hotel_status');
            $table->dropIndex('idx_rooms_room_type_active');
            $table->dropIndex('idx_rooms_status');
        });
    }
};
