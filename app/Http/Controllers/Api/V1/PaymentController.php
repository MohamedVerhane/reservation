<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Reservation;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Payment::whereHas('reservation', fn ($q) => $q->where('user_id', Auth::id()))
            ->with('reservation:id,hotel_id,user_id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        $perPage = min((int) $request->input('per_page', 15), 50);
        $payments = $query->latest()->paginate($perPage);

        return $this->paginatedResponse(PaymentResource::collection($payments));
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $reservation = Reservation::where('id', $request->reservation_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$reservation) {
            return $this->notFoundResponse('Reservation not found.');
        }

        if ($request->amount > $reservation->balance) {
            return $this->errorResponse('Payment amount exceeds the outstanding balance.', 422);
        }

        $payment = Payment::create([
            'reservation_id' => $reservation->id,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'status'         => Payment::STATUS_PENDING,
            'transaction_id' => 'TXN-' . time() . strtoupper(Str::random(8)),
        ]);

        return $this->createdResponse(
            new PaymentResource($payment->load('reservation')),
            'Payment created successfully'
        );
    }

    public function show(Payment $payment): JsonResponse
    {
        $payment->load('reservation:id,user_id');

        if ($payment->reservation->user_id !== Auth::id()) {
            return $this->forbiddenResponse('Unauthorized access to this payment.');
        }

        $payment->load('reservation:id,hotel_id,user_id,check_in,check_out,total_price');

        return $this->successResponse(
            new PaymentResource($payment),
            'Payment retrieved'
        );
    }
}
