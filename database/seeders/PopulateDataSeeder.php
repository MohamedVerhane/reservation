<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Favorite;
use App\Models\Gallery;
use App\Models\Hotel;
use App\Models\Image;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomType;
use App\Models\User;
use App\Notifications\BookingConfirmed;
use App\Notifications\NewBookingAlert;
use App\Notifications\ReviewApproved;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PopulateDataSeeder extends Seeder
{
    /**
     * Existing uploaded media under storage/app/public so the UI
     * actually renders real photos (paths are relative to the public disk).
     */
    private array $hotelCovers = [
        'hotels/CfYrR9WLHf5yMWIKma8FlG05Um5xfbVBDFbsrbTX.png',
        'hotels/Fovn8p5H7STzkN7HC5aO44z6BSuowB75w0PuFaLb.png',
        'hotels/jxqkFBZcVyZvUsRBobkGv6xSdQY9Okvofu0O7K0u.png',
        'hotels/NlawtgyBR8qoXuJQl0jl0m0BbsY8ZC5CTTu6g0yH.jpg',
    ];

    private array $roomImages = [
        'rooms/4JBkHoySy2y6sSDqnsdaP4BLbNM64mLwGZ18CQ78.png',
        'rooms/94NRZmlAAqts5EvTQB3mPYmqKsYFEv2dhHBUKmKG.png',
        'rooms/AVP0784Fn2YxMWYya5g2ULbmdTrh6Sju0y3dBIZV.png',
        'rooms/G5U6SOzga5cC1sFB8kjNvTSOYnCUa8pt7uOrmroB.jpg',
        'rooms/H0UjpHAp0NhEsUqZvh1XYUQy9E018RKct7sAHlEI.png',
        'rooms/iOk8Val1qVxb5QouQ1DKz3R0tpAtqvgSh2OB4vpN.png',
        'rooms/t011jE4r4VVaAOJxOULd5OvA9rEkdWJaa3pHKNZl.png',
        'rooms/VJhJdlhXAhaUwMNreb85imufKlRIhVhw2zZQojjY.png',
        'rooms/vPR8bxoUCcccJYwov2IdOagU0f85YTAVH3cjTIRO.png',
    ];

    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Reset the regenerable tables so the seeder can be re-run safely.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'notifications', 'favorites', 'reviews', 'room_images', 'room_amenities',
            'payments', 'reservations', 'images', 'galleries', 'rooms',
            'room_types', 'hotels', 'amenities',
        ] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::transaction(function (): void {
            $this->seed();
        });

        $this->command?->info('PopulateDataSeeder: seeded users, hotels, room types, rooms, amenities, galleries, images, reservations, payments, reviews, favorites and notifications.');
    }

    private function seed(): void
    {
        $password = Hash::make('password');
        $now = now();

        // ─── Users (updateOrCreate keeps pre-existing rows like the current user) ───
        $admin = $this->user('Admin User', 'admin@example.com', $password, 'admin', $now);
        $owner1 = $this->user('Sidi Ould Ahmed', 'owner1@example.com', $password, 'owner', $now);
        $owner2 = $this->user('Aminata Diallo', 'owner2@example.com', $password, 'owner', $now);
        $guest1 = $this->user('Fatima Mint Ahmed', 'guest1@example.com', $password, 'guest', $now);
        $guest2 = $this->user('Ahmed Ould Mohamed', 'guest2@example.com', $password, 'guest', $now);
        $guest3 = $this->user('Khadija Bint Ali', 'guest3@example.com', $password, 'guest', $now);

        // ─── Hotels ─────────────────────────────────────────────
        $hotel1 = $this->hotel($owner1, [
            'name' => 'Hotel de l Amitié',
            'description' => 'A five-star landmark on the Atlantic coast of Nouakchott, combining Mauritanian hospitality with international standards.',
            'address' => 'Avenue de la République, Tevragh-Zeina',
            'city' => 'Nouakchott',
            'country' => 'Mauritania',
            'phone' => '+222 45 25 40 00',
            'email' => 'contact@amitie-hotel.mr',
            'star_rating' => 5,
            'cover_image' => $this->hotelCovers[0],
        ]);

        $hotel2 = $this->hotel($owner1, [
            'name' => 'Riad Al Kahina',
            'description' => 'A charming riad-style hotel steps from the port, known for its sea-view rooms and fresh seafood restaurant.',
            'address' => 'Boulevard des Phares, Cansado',
            'city' => 'Nouadhibou',
            'country' => 'Mauritania',
            'phone' => '+222 45 34 12 90',
            'email' => 'info@riadal-kahina.mr',
            'star_rating' => 4,
            'cover_image' => $this->hotelCovers[1],
        ]);

        $hotel3 = $this->hotel($owner1, [
            'name' => 'Hotel Tergit',
            'description' => 'A gateway to the ancient caravan city of Ouadane, ideal for exploring the Adrar desert and the Guelb el Richat.',
            'address' => 'Route de l Aeroport',
            'city' => 'Atar',
            'country' => 'Mauritania',
            'phone' => '+222 45 24 88 55',
            'email' => 'stay@tergit-hotel.mr',
            'star_rating' => 3,
            'cover_image' => $this->hotelCovers[2],
        ]);

        $hotel4 = $this->hotel($owner2, [
            'name' => 'Oasis des Sables',
            'description' => 'A welcoming boutique hotel in the heart of Kiffa, perfect for business travellers and families alike.',
            'address' => 'Avenue du Fleuve, Centre Ville',
            'city' => 'Kiffa',
            'country' => 'Mauritania',
            'phone' => '+222 45 52 77 10',
            'email' => 'hello@oasis-sables.mr',
            'star_rating' => 3,
            'cover_image' => $this->hotelCovers[3],
        ]);

        // ─── Room Types ─────────────────────────────────────────
        $roomTypes = [
            $hotel1->id => [
                ['name' => 'Standard Room', 'base_price' => 3800, 'max_guests' => 2, 'max_children' => 1, 'description' => 'Comfortable room with city view.'],
                ['name' => 'Deluxe Room', 'base_price' => 6500, 'max_guests' => 3, 'max_children' => 2, 'description' => 'Spacious room with partial sea view.'],
                ['name' => 'Executive Suite', 'base_price' => 9800, 'max_guests' => 3, 'max_children' => 2, 'description' => 'Suite with separate lounge and sea view.'],
                ['name' => 'Presidential Suite', 'base_price' => 15000, 'max_guests' => 4, 'max_children' => 2, 'description' => 'The ultimate stay, full panoramic sea view.'],
            ],
            $hotel2->id => [
                ['name' => 'Standard Room', 'base_price' => 3000, 'max_guests' => 2, 'max_children' => 1, 'description' => 'Cosy room with city view.'],
                ['name' => 'Sea View Deluxe', 'base_price' => 5800, 'max_guests' => 3, 'max_children' => 1, 'description' => 'Direct sea view over the bay.'],
                ['name' => 'Family Suite', 'base_price' => 7600, 'max_guests' => 5, 'max_children' => 3, 'description' => 'Two connecting rooms for families.'],
            ],
            $hotel3->id => [
                ['name' => 'Standard Room', 'base_price' => 2500, 'max_guests' => 2, 'max_children' => 1, 'description' => 'Simple and clean desert escape.'],
                ['name' => 'Bungalow', 'base_price' => 4000, 'max_guests' => 3, 'max_children' => 2, 'description' => 'Private bungalow in the courtyard.'],
            ],
            $hotel4->id => [
                ['name' => 'Standard Room', 'base_price' => 2600, 'max_guests' => 2, 'max_children' => 1, 'description' => 'Affordable comfort in the city centre.'],
                ['name' => 'Suite', 'base_price' => 5200, 'max_guests' => 4, 'max_children' => 2, 'description' => 'Large suite with sitting area.'],
            ],
        ];

        $roomTypeIds = [];
        foreach ($roomTypes as $hotelId => $types) {
            foreach ($types as $i => $type) {
                $roomTypeIds[$hotelId][$i] = RoomType::create([
                    'hotel_id' => $hotelId,
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'base_price' => $type['base_price'],
                    'max_guests' => $type['max_guests'],
                    'max_children' => $type['max_children'],
                    'is_active' => true,
                ])->id;
            }
        }

        // ─── Rooms (room numbers unique per hotel) ─────────────
        $rooms = [];
        foreach ($roomTypeIds as $hotelId => $typeIds) {
            $rooms[$hotelId] = [];
            $counter = 0;
            foreach ($typeIds as $typeId) {
                for ($i = 1; $i <= 4; $i++) {
                    $counter++;
                    $floor = intdiv($counter - 1, 8) + 1;
                    $rooms[$hotelId][$typeId][] = Room::create([
                        'hotel_id' => $hotelId,
                        'room_type_id' => $typeId,
                        'room_number' => (string) (100 + $counter),
                        'floor' => $floor,
                        'status' => Room::STATUS_AVAILABLE,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // A couple of rooms in non-available states for realism.
        $rooms[$hotel1->id][$roomTypeIds[$hotel1->id][0]][1]->update(['status' => Room::STATUS_MAINTENANCE]);
        $rooms[$hotel1->id][$roomTypeIds[$hotel1->id][0]][2]->update(['status' => Room::STATUS_OUT_OF_ORDER]);
        $rooms[$hotel2->id][$roomTypeIds[$hotel2->id][1]][3]->update(['status' => Room::STATUS_MAINTENANCE]);

        // ─── Room images ───────────────────────────────────────
        $imgIdx = 0;
        foreach ($rooms as $hotelId => $typeMap) {
            foreach ($typeMap as $typeRooms) {
                foreach ($typeRooms as $k => $room) {
                    $imgIdx++;
                    RoomImage::create([
                        'room_id' => $room->id,
                        'path' => $this->roomImages[$imgIdx % count($this->roomImages)],
                        'alt_text' => $room->roomType->name . ' — room ' . $room->room_number,
                        'caption' => $room->roomType->name,
                        'sort_order' => 0,
                        'is_primary' => true,
                    ]);
                }
            }
        }

        // ─── Amenities ─────────────────────────────────────────
        $amenityData = [
            ['name' => 'Free Wi-Fi', 'icon' => 'bi-wifi'],
            ['name' => 'Swimming Pool', 'icon' => 'bi-water'],
            ['name' => 'Fitness Center', 'icon' => 'bi-bicycle'],
            ['name' => 'Restaurant', 'icon' => 'bi-cup-hot'],
            ['name' => 'Spa', 'icon' => 'bi-flower1'],
            ['name' => 'Free Parking', 'icon' => 'bi-car-front'],
            ['name' => 'Air Conditioning', 'icon' => 'bi-snow'],
            ['name' => 'Room Service', 'icon' => 'bi-bell'],
            ['name' => 'Airport Shuttle', 'icon' => 'bi-airplane'],
            ['name' => 'Laundry Service', 'icon' => 'bi-droplet-half'],
        ];

        $amenityIds = [];
        foreach ($amenityData as $a) {
            $amenityIds[] = Amenity::create(['name' => $a['name'], 'icon' => $a['icon'], 'is_active' => true])->id;
        }

        // Attach amenities to rooms: hotel1 rooms get 6, hotel2 5, hotel3 4, hotel4 3.
        $counts = [$hotel1->id => 6, $hotel2->id => 5, $hotel3->id => 4, $hotel4->id => 3];
        foreach ($rooms as $hotelId => $typeMap) {
            foreach ($typeMap as $typeRooms) {
                foreach ($typeRooms as $room) {
                    $room->amenities()->attach(array_slice($amenityIds, 0, $counts[$hotelId]));
                }
            }
        }

        // ─── Galleries & images ────────────────────────────────
        $galleryFiles = array_merge($this->hotelCovers, array_slice($this->roomImages, 0, 4));
        foreach ([$hotel1, $hotel2, $hotel3, $hotel4] as $hotel) {
            for ($g = 1; $g <= 2; $g++) {
                $gallery = Gallery::create([
                    'hotel_id' => $hotel->id,
                    'title' => $hotel->name . ' — Gallery ' . $g,
                    'description' => 'Photo gallery of ' . $hotel->name,
                    'sort_order' => $g,
                ]);
                foreach (array_slice($galleryFiles, 0, 3) as $fi => $file) {
                    Image::create([
                        'gallery_id' => $gallery->id,
                        'path' => $file,
                        'alt_text' => $hotel->name . ' photo',
                        'caption' => null,
                        'sort_order' => $fi,
                    ]);
                }
            }
        }

        // ─── Reservations ──────────────────────────────────────
        $reservation1 = Reservation::create([
            'user_id' => $guest1->id,
            'hotel_id' => $hotel1->id,
            'room_id' => $rooms[$hotel1->id][$roomTypeIds[$hotel1->id][0]][0]->id,
            'check_in' => Carbon::now()->addDays(10),
            'check_out' => Carbon::now()->addDays(14),
            'guests' => 2,
            'children_count' => 0,
            'total_price' => 3800 * 4,
            'status' => Reservation::STATUS_CONFIRMED,
            'notes' => 'Early check-in requested.',
        ]);

        $reservation2 = Reservation::create([
            'user_id' => $guest2->id,
            'hotel_id' => $hotel2->id,
            'room_id' => $rooms[$hotel2->id][$roomTypeIds[$hotel2->id][0]][0]->id,
            'check_in' => Carbon::now()->addDays(20),
            'check_out' => Carbon::now()->addDays(25),
            'guests' => 2,
            'children_count' => 1,
            'total_price' => 3000 * 5,
            'status' => Reservation::STATUS_PENDING,
            'notes' => null,
        ]);

        $reservation3 = Reservation::create([
            'user_id' => $guest1->id,
            'hotel_id' => $hotel2->id,
            'room_id' => $rooms[$hotel2->id][$roomTypeIds[$hotel2->id][1]][0]->id,
            'check_in' => Carbon::now()->subDays(10),
            'check_out' => Carbon::now()->subDays(7),
            'guests' => 2,
            'children_count' => 0,
            'total_price' => 5800 * 3,
            'status' => Reservation::STATUS_CHECKED_OUT,
            'notes' => null,
        ]);

        $reservation4 = Reservation::create([
            'user_id' => $guest2->id,
            'hotel_id' => $hotel1->id,
            'room_id' => $rooms[$hotel1->id][$roomTypeIds[$hotel1->id][1]][0]->id,
            'check_in' => Carbon::now()->subDay(),
            'check_out' => Carbon::now()->addDays(3),
            'guests' => 3,
            'children_count' => 0,
            'total_price' => 6500 * 4,
            'status' => Reservation::STATUS_CHECKED_IN,
            'notes' => 'Birthday surprise for the guest.',
        ]);
        $rooms[$hotel1->id][$roomTypeIds[$hotel1->id][1]][0]->update(['status' => Room::STATUS_OCCUPIED]);

        $reservation5 = Reservation::create([
            'user_id' => $guest3->id,
            'hotel_id' => $hotel3->id,
            'room_id' => $rooms[$hotel3->id][$roomTypeIds[$hotel3->id][0]][0]->id,
            'check_in' => Carbon::now()->subDays(15),
            'check_out' => Carbon::now()->subDays(12),
            'guests' => 1,
            'children_count' => 0,
            'total_price' => 2500 * 3,
            'status' => Reservation::STATUS_CANCELLED,
            'notes' => 'Cancelled due to travel change.',
        ]);

        $reservation6 = Reservation::create([
            'user_id' => $guest3->id,
            'hotel_id' => $hotel4->id,
            'room_id' => $rooms[$hotel4->id][$roomTypeIds[$hotel4->id][1]][0]->id,
            'check_in' => Carbon::now()->addDays(5),
            'check_out' => Carbon::now()->addDays(9),
            'guests' => 4,
            'children_count' => 2,
            'total_price' => 5200 * 4,
            'status' => Reservation::STATUS_CONFIRMED,
            'notes' => null,
        ]);

        // ─── Payments ──────────────────────────────────────────
        $this->payment($reservation1, 15200, 'credit_card', 'completed', 'TXN-' . strtoupper(Str::random(10)), $now);
        $this->payment($reservation2, 15000, 'cash', 'pending', null, null);
        $this->payment($reservation3, 17400, 'credit_card', 'completed', 'TXN-' . strtoupper(Str::random(10)), $now);
        $this->payment($reservation4, 26000, 'online', 'completed', 'TXN-' . strtoupper(Str::random(10)), $now);
        $this->payment($reservation5, 7500, 'bank_transfer', 'refunded', 'TXN-' . strtoupper(Str::random(10)), $now);
        $this->payment($reservation6, 20800, 'online', 'pending', null, null);

        // ─── Reviews ───────────────────────────────────────────
        $review1 = Review::create([
            'user_id' => $guest1->id,
            'hotel_id' => $hotel2->id,
            'reservation_id' => $reservation3->id,
            'rating' => 5,
            'comment' => 'Beautiful sea view and wonderful staff. The seafood restaurant is a must!',
            'reply' => 'Thank you so much for your kind words. We hope to welcome you back soon!',
            'replied_at' => $now,
            'is_approved' => true,
        ]);

        Review::create([
            'user_id' => $guest2->id,
            'hotel_id' => $hotel1->id,
            'reservation_id' => null,
            'rating' => 4,
            'comment' => 'Great rooms and location. Only downside was the slow Wi-Fi in the evening.',
            'reply' => null,
            'replied_at' => null,
            'is_approved' => true,
        ]);

        Review::create([
            'user_id' => $guest1->id,
            'hotel_id' => $hotel1->id,
            'reservation_id' => $reservation1->id,
            'rating' => 3,
            'comment' => 'Nice hotel overall but the pool was under maintenance during our stay.',
            'reply' => null,
            'replied_at' => null,
            'is_approved' => false,
        ]);

        $review4 = Review::create([
            'user_id' => $guest3->id,
            'hotel_id' => $hotel4->id,
            'reservation_id' => $reservation6->id,
            'rating' => 5,
            'comment' => 'A hidden gem in Kiffa. Extremely friendly and very clean rooms.',
            'reply' => 'We are delighted you enjoyed your stay. Merci beaucoup!',
            'replied_at' => $now,
            'is_approved' => true,
        ]);

        // ─── Favorites ─────────────────────────────────────────
        Favorite::create(['user_id' => $guest1->id, 'hotel_id' => $hotel1->id]);
        Favorite::create(['user_id' => $guest1->id, 'hotel_id' => $hotel2->id]);
        Favorite::create(['user_id' => $guest2->id, 'hotel_id' => $hotel3->id]);
        Favorite::create(['user_id' => $guest3->id, 'hotel_id' => $hotel4->id]);

        // ─── Notifications ─────────────────────────────────────
        $this->notification($guest1, BookingConfirmed::class, [
            'type' => 'booking_confirmed',
            'reservation_id' => $reservation1->id,
            'hotel_name' => $hotel1->name,
            'hotel_slug' => $hotel1->slug,
            'room_name' => $reservation1->room->display_name,
            'check_in' => $reservation1->check_in->format('Y-m-d'),
            'check_out' => $reservation1->check_out->format('Y-m-d'),
            'total_price' => $reservation1->total_price,
            'message' => 'Your booking has been confirmed.',
        ]);

        $this->notification($owner1, NewBookingAlert::class, [
            'type' => 'new_booking',
            'reservation_id' => $reservation1->id,
            'guest_name' => $guest1->name,
            'guest_email' => $guest1->email,
            'hotel_name' => $hotel1->name,
            'room_name' => $reservation1->room->display_name,
            'check_in' => $reservation1->check_in->format('Y-m-d'),
            'check_out' => $reservation1->check_out->format('Y-m-d'),
            'total_price' => $reservation1->total_price,
            'message' => 'A new booking has been placed.',
        ]);

        $this->notification($admin, NewBookingAlert::class, [
            'type' => 'new_booking',
            'reservation_id' => $reservation2->id,
            'guest_name' => $guest2->name,
            'guest_email' => $guest2->email,
            'hotel_name' => $hotel2->name,
            'room_name' => $reservation2->room->display_name,
            'check_in' => $reservation2->check_in->format('Y-m-d'),
            'check_out' => $reservation2->check_out->format('Y-m-d'),
            'total_price' => $reservation2->total_price,
            'message' => 'A new booking has been placed.',
        ]);

        $this->notification($guest1, ReviewApproved::class, [
            'type' => 'review_approved',
            'review_id' => $review1->id,
            'hotel_name' => $hotel2->name,
            'hotel_slug' => $hotel2->slug,
            'rating' => $review1->rating,
            'message' => 'Your review has been approved.',
        ]);

        $this->notification($guest3, ReviewApproved::class, [
            'type' => 'review_approved',
            'review_id' => $review4->id,
            'hotel_name' => $hotel4->name,
            'hotel_slug' => $hotel4->slug,
            'rating' => $review4->rating,
            'message' => 'Your review has been approved.',
        ]);
    }

    private function user(string $name, string $email, string $password, string $role, Carbon $now): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => null,
                'role' => $role,
                'password' => $password,
                'email_verified_at' => $now,
            ]
        );
        $user->assignRole($role);

        return $user;
    }

    private function hotel(User $owner, array $attributes): Hotel
    {
        $attributes['user_id'] = $owner->id;
        $attributes['is_active'] = true;

        return Hotel::create($attributes);
    }

    private function payment(
        Reservation $reservation,
        float $amount,
        string $method,
        string $status,
        ?string $transactionId,
        ?Carbon $paidAt,
    ): void {
        Payment::create([
            'reservation_id' => $reservation->id,
            'amount' => $amount,
            'method' => $method,
            'status' => $status,
            'transaction_id' => $transactionId,
            'paid_at' => $paidAt,
        ]);
    }

    private function notification(User $user, string $type, array $data): void
    {
        $user->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => $type,
            'data' => $data,
        ]);
    }
}
