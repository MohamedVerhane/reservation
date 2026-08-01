<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Hotel $hotel;
    private RoomType $roomType;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->hotel = Hotel::factory()->create();
        $this->roomType = RoomType::factory()->for($this->hotel)->create(['base_price' => 100]);
        $this->room = Room::factory()->for($this->hotel)->for($this->roomType)->available()->create();
    }

    public function test_booking_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get("/book/{$this->hotel->slug}");

        $response->assertStatus(200);
    }

    public function test_check_availability_returns_available_room_types(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'room_types',
                'nights',
                'check_in',
                'check_out',
            ]);

        $this->assertCount(1, $response->json('room_types'));
    }

    public function test_check_availability_excludes_reserved_rooms(): void
    {
        Reservation::factory()
            ->for($this->hotel)
            ->for($this->room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(5),
                Carbon::now()->addDays(7)
            )
            ->create();

        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(0, 'room_types');
    }

    public function test_booking_fails_with_past_check_in_date(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->subDay()->toDateString(),
            'check_out' => Carbon::now()->addDays(3)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_booking_fails_with_check_out_before_check_in(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(2)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_booking_fails_with_invalid_hotel(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => 999,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_select_room_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/book/select-room', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'room_type_id' => $this->roomType->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_review_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/book/review', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
        ]);

        $response->assertStatus(200);
    }

    public function test_booking_can_be_created(): void
    {
        $this->actingAs($this->user);

        $response = $this->post('/book/confirm', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseHas('payments', [
            'method' => Payment::METHOD_CREDIT_CARD,
        ]);
    }

    public function test_double_booking_is_prevented(): void
    {
        Reservation::factory()
            ->for($this->hotel)
            ->for($this->room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(5),
                Carbon::now()->addDays(7)
            )
            ->create();

        $this->actingAs($this->user);

        $response = $this->post('/book/confirm', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
        ]);

        $response->assertSessionHasErrors('booking');
    }

    public function test_my_bookings_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-bookings');

        $response->assertStatus(200);
    }

    public function test_booking_confirmation_page_is_displayed(): void
    {
        $reservation = Reservation::factory()
            ->for($this->user)
            ->for($this->hotel)
            ->for($this->room)
            ->confirmed()
            ->create();

        $this->actingAs($this->user);

        $response = $this->get("/booking/{$reservation->id}/confirmation");

        $response->assertStatus(200);
    }

    public function test_booking_confirmation_requires_auth(): void
    {
        $reservation = Reservation::factory()
            ->for($this->hotel)
            ->for($this->room)
            ->confirmed()
            ->create();

        $response = $this->get("/booking/{$reservation->id}/confirmation");

        $response->assertRedirect('/login');
    }
}
