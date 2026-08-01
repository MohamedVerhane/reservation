<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->hotel = Hotel::factory()->create();
    }

    public function test_review_can_be_created(): void
    {
        $this->actingAs($this->user);

        $response = $this->post("/hotels/{$this->hotel->slug}/reviews", [
            'rating' => 5,
            'comment' => 'Excellent hotel!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id,
            'rating' => 5,
            'comment' => 'Excellent hotel!',
            'is_approved' => false,
        ]);
    }

    public function test_review_requires_authentication(): void
    {
        $response = $this->postJson("/hotels/{$this->hotel->slug}/reviews", [
            'rating' => 5,
            'comment' => 'Excellent hotel!',
        ]);

        $response->assertStatus(401);
    }

    public function test_review_requires_valid_rating(): void
    {
        $this->actingAs($this->user);

        $response = $this->post("/hotels/{$this->hotel->slug}/reviews", [
            'rating' => 6,
            'comment' => 'Excellent hotel!',
        ]);

        $response->assertInvalid('rating');
    }

    public function test_review_can_be_deleted_by_owner(): void
    {
        $review = Review::factory()
            ->for($this->user)
            ->for($this->hotel)
            ->create();

        $this->actingAs($this->user);

        $response = $this->delete("/hotels/{$this->hotel->slug}/reviews/{$review->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }

    public function test_review_cannot_be_deleted_by_other_user(): void
    {
        $review = Review::factory()
            ->for($this->hotel)
            ->create();

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $response = $this->delete("/hotels/{$this->hotel->slug}/reviews/{$review->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_approve_review(): void
    {
        $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
        $review = Review::factory()->pending()->for($this->hotel)->create();

        $this->actingAs($admin);

        $response = $this->patch("/admin/reviews/{$review->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);
    }

    public function test_admin_can_reject_review(): void
    {
        $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
        $review = Review::factory()->approved()->for($this->hotel)->create();

        $this->actingAs($admin);

        $response = $this->patch("/admin/reviews/{$review->id}/reject");

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_reply_to_review(): void
    {
        $admin = User::factory()->admin()->create(['email_verified_at' => now()]);
        $review = Review::factory()->approved()->for($this->hotel)->create();

        $this->actingAs($admin);

        $response = $this->patch("/admin/reviews/{$review->id}/reply", [
            'reply' => 'Thank you for your feedback!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'reply' => 'Thank you for your feedback!',
        ]);
    }
}
