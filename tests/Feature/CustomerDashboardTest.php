<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_dashboard_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard');

        $response->assertStatus(200);
    }

    public function test_reservations_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/reservations');

        $response->assertStatus(200);
    }

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/profile');

        $response->assertStatus(200);
    }

    public function test_profile_can_be_updated(): void
    {
        $this->actingAs($this->user);

        $response = $this->put('/my-dashboard/profile', [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'phone' => '1234567890',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_reviews_page_is_displayed(): void
    {
        $hotels = Hotel::factory()->count(3)->create();
        foreach ($hotels as $hotel) {
            Review::factory()->for($this->user)->for($hotel)->create();
        }

        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/reviews');

        $response->assertStatus(200);
    }

    public function test_favorites_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/favorites');

        $response->assertStatus(200);
    }

    public function test_favorite_can_be_toggled(): void
    {
        $hotel = Hotel::factory()->create();

        $this->actingAs($this->user);

        $response = $this->postJson('/my-dashboard/favorites/toggle', [
            'hotel_id' => $hotel->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_favorited' => true,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'hotel_id' => $hotel->id,
        ]);
    }

    public function test_favorite_can_be_removed(): void
    {
        $hotel = Hotel::factory()->create();
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'hotel_id' => $hotel->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->postJson('/my-dashboard/favorites/toggle', [
            'hotel_id' => $hotel->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'is_favorited' => false,
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'hotel_id' => $hotel->id,
        ]);
    }

    public function test_invoices_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/invoices');

        $response->assertStatus(200);
    }

    public function test_history_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/history');

        $response->assertStatus(200);
    }

    public function test_notifications_page_is_displayed(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/my-dashboard/notifications');

        $response->assertStatus(200);
    }
}
