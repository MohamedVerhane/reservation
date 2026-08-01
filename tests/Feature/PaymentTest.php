<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $roomType = \App\Models\RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->available()->create();

        $this->reservation = Reservation::factory()
            ->for($this->user)
            ->for($hotel)
            ->for($room)
            ->confirmed()
            ->create(['total_price' => 300]);
    }

    public function test_payment_can_be_created(): void
    {
        $payment = Payment::factory()
            ->for($this->reservation)
            ->create([
                'amount' => 300,
                'method' => Payment::METHOD_CREDIT_CARD,
                'status' => Payment::STATUS_PENDING,
            ]);

        $this->assertDatabaseHas('payments', [
            'reservation_id' => $this->reservation->id,
            'amount' => 300,
            'method' => Payment::METHOD_CREDIT_CARD,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function test_payment_can_be_marked_as_completed(): void
    {
        $payment = Payment::factory()
            ->for($this->reservation)
            ->pending()
            ->create();

        $payment->markAsCompleted('TXN-12345');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_COMPLETED,
            'transaction_id' => 'TXN-12345',
        ]);
        $this->assertNotNull($payment->fresh()->paid_at);
    }

    public function test_payment_can_be_marked_as_failed(): void
    {
        $payment = Payment::factory()
            ->for($this->reservation)
            ->pending()
            ->create();

        $payment->markAsFailed();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_FAILED,
        ]);
    }

    public function test_payment_can_be_marked_as_refunded(): void
    {
        $payment = Payment::factory()
            ->for($this->reservation)
            ->completed()
            ->create();

        $payment->markAsRefunded();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => Payment::STATUS_REFUNDED,
        ]);
    }

    public function test_completed_payment_scope(): void
    {
        Payment::factory()->for($this->reservation)->completed()->create();
        Payment::factory()->for($this->reservation)->pending()->create();

        $completedPayments = Payment::completed()->get();

        $this->assertCount(1, $completedPayments);
    }

    public function test_pending_payment_scope(): void
    {
        Payment::factory()->for($this->reservation)->completed()->create();
        Payment::factory()->for($this->reservation)->pending()->create();

        $pendingPayments = Payment::pending()->get();

        $this->assertCount(1, $pendingPayments);
    }

    public function test_payment_method_labels(): void
    {
        $this->assertEquals('Cash', (new Payment(['method' => Payment::METHOD_CASH]))->method_label);
        $this->assertEquals('Credit Card', (new Payment(['method' => Payment::METHOD_CREDIT_CARD]))->method_label);
        $this->assertEquals('Debit Card', (new Payment(['method' => Payment::METHOD_DEBIT_CARD]))->method_label);
        $this->assertEquals('Bank Transfer', (new Payment(['method' => Payment::METHOD_BANK_TRANSFER]))->method_label);
        $this->assertEquals('Online', (new Payment(['method' => Payment::METHOD_ONLINE]))->method_label);
    }
}
