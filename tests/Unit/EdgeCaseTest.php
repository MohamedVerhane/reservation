<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_model_nights_accessor(): void
    {
        $reservation = Reservation::factory()
            ->forDates(
                Carbon::parse('2026-08-01'),
                Carbon::parse('2026-08-04')
            )
            ->create();

        $this->assertEquals(3, $reservation->nights);
    }

    public function test_reservation_can_be_cancelled_when_pending(): void
    {
        $reservation = Reservation::factory()->pending()->upcoming()->create();

        $this->assertTrue($reservation->canBeCancelled());
    }

    public function test_reservation_can_be_cancelled_when_confirmed(): void
    {
        $reservation = Reservation::factory()->confirmed()->upcoming()->create();

        $this->assertTrue($reservation->canBeCancelled());
    }

    public function test_reservation_cannot_be_cancelled_when_checked_in(): void
    {
        $reservation = Reservation::factory()->checkedIn()->active()->create();

        $this->assertFalse($reservation->canBeCancelled());
    }

    public function test_reservation_cannot_be_cancelled_when_past(): void
    {
        $reservation = Reservation::factory()->checkedOut()->past()->create();

        $this->assertFalse($reservation->canBeCancelled());
    }

    public function test_reservation_can_be_checked_in_when_confirmed_and_today(): void
    {
        $reservation = Reservation::factory()->confirmed()
            ->forDates(now(), now()->addDays(3))
            ->create();

        $this->assertTrue($reservation->canBeCheckedIn());
    }

    public function test_reservation_can_be_checked_in_when_confirmed_and_past(): void
    {
        $reservation = Reservation::factory()->confirmed()
            ->forDates(now()->subDay(), now()->addDays(3))
            ->create();

        $this->assertTrue($reservation->canBeCheckedIn());
    }

    public function test_reservation_cannot_be_checked_in_when_pending(): void
    {
        $reservation = Reservation::factory()->pending()->upcoming()->create();

        $this->assertFalse($reservation->canBeCheckedIn());
    }

    public function test_reservation_can_be_checked_out_when_checked_in(): void
    {
        $reservation = Reservation::factory()->checkedIn()->create();

        $this->assertTrue($reservation->canBeCheckedOut());
    }

    public function test_reservation_cannot_be_checked_out_when_confirmed(): void
    {
        $reservation = Reservation::factory()->confirmed()->upcoming()->create();

        $this->assertFalse($reservation->canBeCheckedOut());
    }

    public function test_check_in_updates_room_status(): void
    {
        $room = Room::factory()->available()->create();
        $reservation = Reservation::factory()->confirmed()->for($room->hotel)->for($room)
            ->forDates(now(), now()->addDays(3))
            ->create();

        $reservation->checkIn();

        $this->assertEquals('occupied', $room->fresh()->status);
    }

    public function test_check_out_updates_room_status(): void
    {
        $room = Room::factory()->occupied()->create();
        $reservation = Reservation::factory()->checkedIn()->for($room->hotel)->for($room)->create();

        $reservation->checkOut();

        $this->assertEquals('available', $room->fresh()->status);
    }

    public function test_cancel_updates_room_status_when_occupied(): void
    {
        $room = Room::factory()->occupied()->create();
        $reservation = Reservation::factory()->checkedIn()->for($room->hotel)->for($room)->create();

        $reservation->cancel();

        $this->assertEquals('available', $room->fresh()->status);
    }

    public function test_cancel_does_not_update_room_status_when_not_occupied(): void
    {
        $room = Room::factory()->available()->create();
        $reservation = Reservation::factory()->confirmed()->for($room->hotel)->for($room)->upcoming()->create();

        $reservation->cancel();

        $this->assertEquals('available', $room->fresh()->status);
    }

    public function test_payment_status_accessors(): void
    {
        $completed = new Payment(['status' => Payment::STATUS_COMPLETED]);
        $pending = new Payment(['status' => Payment::STATUS_PENDING]);
        $failed = new Payment(['status' => Payment::STATUS_FAILED]);
        $refunded = new Payment(['status' => Payment::STATUS_REFUNDED]);

        $this->assertTrue($completed->is_completed);
        $this->assertFalse($completed->is_pending);

        $this->assertTrue($pending->is_pending);
        $this->assertFalse($pending->is_completed);

        $this->assertTrue($failed->is_failed);
        $this->assertFalse($failed->is_completed);

        $this->assertTrue($refunded->is_refunded);
        $this->assertFalse($refunded->is_completed);
    }

    public function test_review_approval_workflow(): void
    {
        $review = Review::factory()->pending()->create();

        $this->assertTrue($review->isPending());
        $this->assertFalse($review->isApproved());

        $review->approve();

        $this->assertFalse($review->fresh()->isPending());
        $this->assertTrue($review->fresh()->isApproved());
    }

    public function test_review_reply_workflow(): void
    {
        $review = Review::factory()->approved()->create();

        $this->assertFalse($review->has_reply);

        $review->addReply('Thank you for your feedback!');

        $this->assertTrue($review->fresh()->has_reply);
        $this->assertEquals('Thank you for your feedback!', $review->fresh()->reply);
    }

    public function test_user_role_helpers(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->owner()->create();
        $guest = User::factory()->guest()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isOwner());
        $this->assertFalse($admin->isGuest());

        $this->assertTrue($owner->isOwner());
        $this->assertFalse($owner->isAdmin());
        $this->assertFalse($owner->isGuest());

        $this->assertTrue($guest->isGuest());
        $this->assertFalse($guest->isAdmin());
        $this->assertFalse($guest->isOwner());
    }

    public function test_hotel_is_owned_by_user(): void
    {
        $user = User::factory()->create();
        $hotel = \App\Models\Hotel::factory()->for($user)->create();

        $this->assertTrue($hotel->isOwnedBy($user));

        $otherUser = User::factory()->create();
        $this->assertFalse($hotel->isOwnedBy($otherUser));

        $this->assertTrue($user->ownsHotel($hotel));
        $this->assertFalse($otherUser->ownsHotel($hotel));
    }

    public function test_hotel_star_rating_label(): void
    {
        $hotel1 = new \App\Models\Hotel(['star_rating' => 1]);
        $hotel2 = new \App\Models\Hotel(['star_rating' => 2]);
        $hotel3 = new \App\Models\Hotel(['star_rating' => 3]);
        $hotel4 = new \App\Models\Hotel(['star_rating' => 4]);
        $hotel5 = new \App\Models\Hotel(['star_rating' => 5]);

        $this->assertEquals('Economy', $hotel1->star_rating_label);
        $this->assertEquals('Budget', $hotel2->star_rating_label);
        $this->assertEquals('Standard', $hotel3->star_rating_label);
        $this->assertEquals('Superior', $hotel4->star_rating_label);
        $this->assertEquals('Luxury', $hotel5->star_rating_label);
    }

    public function test_room_type_calculate_price(): void
    {
        $roomType = \App\Models\RoomType::factory()->create(['base_price' => 100]);

        $this->assertEquals(300, $roomType->calculatePrice(3));
        $this->assertEquals(700, $roomType->calculatePrice(7));
    }
}
