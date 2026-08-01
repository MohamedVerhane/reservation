<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService();
    }

    public function test_calculate_nights(): void
    {
        $checkIn = Carbon::parse('2026-08-01');
        $checkOut = Carbon::parse('2026-08-04');

        $result = $this->service->calculateNights($checkIn, $checkOut);

        $this->assertEquals(3, $result);
    }

    public function test_calculate_nights_same_day_returns_zero(): void
    {
        $date = Carbon::parse('2026-08-01');

        $result = $this->service->calculateNights($date, $date);

        $this->assertEquals(0, $result);
    }

    public function test_validate_dates_with_valid_dates(): void
    {
        $checkIn = Carbon::now()->addDay();
        $checkOut = Carbon::now()->addDays(3);

        $errors = $this->service->validateDates($checkIn, $checkOut);

        $this->assertEmpty($errors);
    }

    public function test_validate_dates_with_past_check_in(): void
    {
        $checkIn = Carbon::now()->subDay();
        $checkOut = Carbon::now()->addDays(3);

        $errors = $this->service->validateDates($checkIn, $checkOut);

        $this->assertArrayHasKey('check_in', $errors);
    }

    public function test_validate_dates_with_check_out_before_check_in(): void
    {
        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(2);

        $errors = $this->service->validateDates($checkIn, $checkOut);

        $this->assertArrayHasKey('check_out', $errors);
    }

    public function test_validate_dates_with_stay_longer_than_30_days(): void
    {
        $checkIn = Carbon::now()->addDay();
        $checkOut = Carbon::now()->addDays(32);

        $errors = $this->service->validateDates($checkIn, $checkOut);

        $this->assertArrayHasKey('check_out', $errors);
    }

    public function test_is_room_available_when_no_reservations(): void
    {
        $room = Room::factory()->available()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertTrue($result);
    }

    public function test_is_room_available_when_room_is_inactive(): void
    {
        $room = Room::factory()->inactive()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertFalse($result);
    }

    public function test_is_room_available_when_room_is_out_of_order(): void
    {
        $room = Room::factory()->outOfOrder()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertFalse($result);
    }

    public function test_is_room_available_when_overlapping_reservation_exists(): void
    {
        $room = Room::factory()->available()->create();

        Reservation::factory()
            ->for($room->hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(4),
                Carbon::now()->addDays(7)
            )
            ->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertFalse($result);
    }

    public function test_is_room_available_when_existing_reservation_is_cancelled(): void
    {
        $room = Room::factory()->available()->create();

        Reservation::factory()
            ->for($room->hotel)
            ->for($room)
            ->cancelled()
            ->forDates(
                Carbon::now()->addDays(4),
                Carbon::now()->addDays(7)
            )
            ->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertTrue($result);
    }

    public function test_is_room_available_with_non_overlapping_dates(): void
    {
        $room = Room::factory()->available()->create();

        Reservation::factory()
            ->for($room->hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(1),
                Carbon::now()->addDays(3)
            )
            ->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->isRoomAvailable($room->id, $checkIn, $checkOut);

        $this->assertTrue($result);
    }

    public function test_generate_transaction_id_has_correct_format(): void
    {
        $transactionId = $this->service->generateTransactionId();

        $this->assertMatchesRegularExpression('/^TXN-\d{10}[A-Z0-9]{8}$/', $transactionId);
    }

    public function test_create_reservation_successfully(): void
    {
        $room = Room::factory()->available()->create();

        $validated = [
            'hotel_id' => $room->hotel_id,
            'room_id' => $room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 1,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
            'special_requests' => 'Late check-in please',
        ];

        $userId = $room->hotel->user_id;

        $result = $this->service->createReservation($validated, $userId);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['reservation']);
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $result['reservation']->status);
        $this->assertDatabaseHas('reservations', [
            'hotel_id' => $room->hotel_id,
            'room_id' => $room->id,
            'user_id' => $userId,
            'status' => Reservation::STATUS_CONFIRMED,
        ]);
        $this->assertDatabaseHas('payments', [
            'reservation_id' => $result['reservation']->id,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function test_create_reservation_fails_when_room_not_found(): void
    {
        $validated = [
            'hotel_id' => 999,
            'room_id' => 999,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
        ];

        $result = $this->service->createReservation($validated, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['status']);
    }

    public function test_create_reservation_fails_when_room_not_available(): void
    {
        $room = Room::factory()->available()->create();

        Reservation::factory()
            ->for($room->hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(5),
                Carbon::now()->addDays(7)
            )
            ->create();

        $validated = [
            'hotel_id' => $room->hotel_id,
            'room_id' => $room->id,
            'check_in' => Carbon::now()->addDays(5)->toDateString(),
            'check_out' => Carbon::now()->addDays(8)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'payment_method' => Payment::METHOD_CREDIT_CARD,
        ];

        $result = $this->service->createReservation($validated, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status']);
    }
}
