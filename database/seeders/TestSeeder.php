<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Favorite;
use App\Models\Gallery;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Users ───────────────────────────────────────────
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $guest1 = User::factory()->guest()->create([
            'name' => 'Guest One',
            'email' => 'guest1@test.com',
            'email_verified_at' => now(),
        ]);
        $guest1->assignRole('guest');

        $guest2 = User::factory()->guest()->create([
            'name' => 'Guest Two',
            'email' => 'guest2@test.com',
            'email_verified_at' => now(),
        ]);
        $guest2->assignRole('guest');

        // ─── Hotels ─────────────────────────────────────────
        $hotel1 = Hotel::factory()->for($admin)->create([
            'name' => 'Grand Palace Hotel',
            'city' => 'Dubai',
            'country' => 'UAE',
            'star_rating' => 5,
            'is_active' => true,
        ]);

        $hotel2 = Hotel::factory()->for($admin)->create([
            'name' => 'Seaside Resort',
            'city' => 'Miami',
            'country' => 'USA',
            'star_rating' => 4,
            'is_active' => true,
        ]);

        // ─── Room Types ──────────────────────────────────────
        $standard = RoomType::factory()->for($hotel1)->create([
            'name' => 'Standard Room',
            'base_price' => 150.00,
            'max_guests' => 2,
        ]);

        $deluxe = RoomType::factory()->for($hotel1)->create([
            'name' => 'Deluxe Suite',
            'base_price' => 350.00,
            'max_guests' => 3,
        ]);

        $penthouse = RoomType::factory()->for($hotel1)->create([
            'name' => 'Penthouse Suite',
            'base_price' => 800.00,
            'max_guests' => 4,
        ]);

        $beachRoom = RoomType::factory()->for($hotel2)->create([
            'name' => 'Beachfront Room',
            'base_price' => 220.00,
            'max_guests' => 2,
        ]);

        // ─── Rooms ───────────────────────────────────────────
        Room::factory()->count(5)->for($hotel1)->for($standard)->available()->create();
        Room::factory()->count(3)->for($hotel1)->for($deluxe)->available()->create();
        Room::factory()->count(2)->for($hotel1)->for($penthouse)->available()->create();
        Room::factory()->count(1)->for($hotel1)->for($standard)->maintenance()->create();
        Room::factory()->count(4)->for($hotel2)->for($beachRoom)->available()->create();

        // ─── Amenities ──────────────────────────────────────
        $amenities = [
            ['name' => 'Free Wi-Fi', 'icon' => 'wifi'],
            ['name' => 'Swimming Pool', 'icon' => 'swimming-pool'],
            ['name' => 'Fitness Center', 'icon' => 'dumbbell'],
            ['name' => 'Restaurant', 'icon' => 'utensils'],
            ['name' => 'Spa', 'icon' => 'spa'],
            ['name' => 'Parking', 'icon' => 'car'],
            ['name' => 'Room Service', 'icon' => 'concierge-bell'],
            ['name' => 'Business Center', 'icon' => 'briefcase'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }

        $hotel1->amenities()->attach(Amenity::all()->pluck('id')->take(6));
        $hotel2->amenities()->attach(Amenity::all()->pluck('id')->take(4));

        // ─── Galleries ──────────────────────────────────────
        Gallery::factory()->count(3)->for($hotel1)->create();
        Gallery::factory()->count(2)->for($hotel2)->create();

        // ─── Reservations & Payments ─────────────────────────
        $room1 = $hotel1->rooms()->for($standard)->available()->first();
        $room2 = $hotel1->rooms()->for($deluxe)->available()->first();

        $reservation1 = Reservation::factory()->for($guest1)->for($hotel1)->for($room1)
            ->confirmed()->upcoming()
            ->forDates(
                Carbon::now()->addDays(10),
                Carbon::now()->addDays(14)
            )
            ->create(['total_price' => 600.00]);

        Payment::factory()->for($reservation1)->completed()
            ->create(['amount' => 600.00, 'method' => 'credit_card']);

        $reservation2 = Reservation::factory()->for($guest2)->for($hotel1)->for($room2)
            ->pending()->upcoming()
            ->forDates(
                Carbon::now()->addDays(20),
                Carbon::now()->addDays(25)
            )
            ->create(['total_price' => 1750.00]);

        $reservation3 = Reservation::factory()->for($guest1)->for($hotel2)
            ->for($hotel2->rooms()->for($beachRoom)->available()->first())
            ->checkedOut()->past()
            ->forDates(
                Carbon::now()->subDays(10),
                Carbon::now()->subDays(7)
            )
            ->create(['total_price' => 660.00]);

        Payment::factory()->for($reservation3)->completed()
            ->create(['amount' => 660.00, 'method' => 'credit_card']);

        // ─── Reviews ─────────────────────────────────────────
        Review::factory()->for($guest1)->for($hotel1)->approved()
            ->forReservation($reservation3)
            ->create([
                'rating' => 5,
                'comment' => 'Absolutely stunning hotel! The rooms were spotless and the staff were incredibly attentive.',
                'reply' => 'Thank you so much for your wonderful review! We hope to welcome you back soon.',
                'replied_at' => now(),
            ]);

        Review::factory()->for($guest2)->for($hotel1)->approved()
            ->create([
                'rating' => 4,
                'comment' => 'Great location and amenities. Only minor issue was the Wi-Fi speed.',
            ]);

        Review::factory()->for($guest1)->for($hotel2)->pending()
            ->create([
                'rating' => 3,
                'comment' => 'Beautiful views but the room was smaller than expected.',
            ]);

        // ─── Favorites ──────────────────────────────────────
        Favorite::factory()->for($guest1)->for($hotel1)->create();
        Favorite::factory()->for($guest1)->for($hotel2)->create();
        Favorite::factory()->for($guest2)->for($hotel1)->create();
    }
}
