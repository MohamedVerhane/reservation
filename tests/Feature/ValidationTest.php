<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
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
        $this->roomType = RoomType::factory()->for($this->hotel)->create();
        $this->room = Room::factory()->for($this->hotel)->for($this->roomType)->available()->create();
    }

    public function test_check_availability_requires_hotel_id(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('hotel_id');
    }

    public function test_check_availability_requires_check_in(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('check_in');
    }

    public function test_check_availability_requires_check_out(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('check_out');
    }

    public function test_check_availability_requires_adults(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('adults');
    }

    public function test_check_availability_validates_hotel_exists(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/check-availability', [
            'hotel_id' => 999,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('hotel_id');
    }

    public function test_select_room_requires_room_type_id(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/select-room', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('room_type_id');
    }

    public function test_review_requires_room_id(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/review', [
            'hotel_id' => $this->hotel->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('room_id');
    }

    public function test_booking_requires_payment_method(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/book/confirm', [
            'hotel_id' => $this->hotel->id,
            'room_id' => $this->room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('payment_method');
    }

    public function test_profile_update_requires_name(): void
    {
        $this->actingAs($this->user);

        $response = $this->putJson('/my-dashboard/profile', [
            'email' => $this->user->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_profile_update_requires_valid_email(): void
    {
        $this->actingAs($this->user);

        $response = $this->putJson('/my-dashboard/profile', [
            'name' => 'Test User',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_profile_update_requires_unique_email(): void
    {
        $otherUser = User::factory()->create();

        $this->actingAs($this->user);

        $response = $this->putJson('/my-dashboard/profile', [
            'name' => 'Test User',
            'email' => $otherUser->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}
