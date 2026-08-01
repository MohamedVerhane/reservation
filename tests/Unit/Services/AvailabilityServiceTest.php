<?php

namespace Tests\Unit;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AvailabilityService();
    }

    public function test_get_available_room_types_returns_available_types(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->getAvailableRoomTypes($hotel->id, $checkIn, $checkOut);

        $this->assertNotEmpty($result);
        $this->assertEquals($roomType->id, $result[0]->id);
        $this->assertEquals(1, $result[0]->available_count);
    }

    public function test_get_available_room_types_excludes_inactive_types(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->inactive()->create();
        Room::factory()->for($hotel)->for($roomType)->available()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->getAvailableRoomTypes($hotel->id, $checkIn, $checkOut);

        $this->assertEmpty($result);
    }

    public function test_get_available_room_types_excludes_reserved_rooms(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        Reservation::factory()
            ->for($hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(5),
                Carbon::now()->addDays(7)
            )
            ->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->getAvailableRoomTypes($hotel->id, $checkIn, $checkOut);

        $this->assertEmpty($result);
    }

    public function test_get_available_rooms_returns_available_rooms(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->getAvailableRooms($hotel->id, $roomType->id, $checkIn, $checkOut);

        $this->assertCount(1, $result);
        $this->assertEquals($room->id, $result->first()->id);
    }

    public function test_get_available_rooms_excludes_reserved_rooms(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        Reservation::factory()
            ->for($hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::now()->addDays(5),
                Carbon::now()->addDays(7)
            )
            ->create();

        $checkIn = Carbon::now()->addDays(5);
        $checkOut = Carbon::now()->addDays(8);

        $result = $this->service->getAvailableRooms($hotel->id, $roomType->id, $checkIn, $checkOut);

        $this->assertEmpty($result);
    }

    public function test_get_calendar_availability_returns_correct_structure(): void
    {
        $hotel = Hotel::factory()->create();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $result = $this->service->getCalendarAvailability($hotel->id, $month, $year);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('date', $result[0]);
        $this->assertArrayHasKey('available', $result[0]);
        $this->assertArrayHasKey('total', $result[0]);
        $this->assertArrayHasKey('booked', $result[0]);
    }

    public function test_get_calendar_availability_accounts_for_reservations(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        Reservation::factory()
            ->for($hotel)
            ->for($room)
            ->confirmed()
            ->forDates(
                Carbon::createFromDate($year, $month, 10),
                Carbon::createFromDate($year, $month, 15)
            )
            ->create();

        $result = $this->service->getCalendarAvailability($hotel->id, $month, $year);

        $this->assertEquals(1, $result[9]['booked']); // Day 10 should have 1 booking
        $this->assertEquals($room->id ? 0 : 1, $result[9]['available']); // Available = total - booked
    }
}
