<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['reservation.user', 'reservation.hotel', 'reservation.room']);

        if ($request->filled('search')) {
            $search = '%' . addslashes($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', $search)
                    ->orWhereHas('reservation', function ($q2) use ($search) {
                        $q2->where('id', 'like', $search)
                            ->orWhereHas('user', function ($q3) use ($search) {
                                $q3->where('name', 'like', $search)
                                    ->orWhere('email', 'like', $search);
                            });
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['id', 'amount', 'status', 'method', 'created_at', 'paid_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function create(): View
    {
        return view('admin.payments.create');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['reservation.user', 'reservation.hotel', 'reservation.room', 'reservation.payments']);

        return view('admin.payments.show', compact('payment'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse|JsonResponse
    {
        Payment::create([
            'reservation_id' => $request->reservation_id,
            'amount' => $request->amount,
            'method' => $request->method,
            'transaction_id' => $request->transaction_id,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.reservations.show', $request->reservation_id)
            ->with('success', __('admin.payment.created'))
            ->orJson();
    }

    public function updateStatus(Payment $payment, Request $request): RedirectResponse
    {
        $this->authorize('update', $payment);

        $request->validate([
            'status' => 'required|in:completed,failed,refunded',
        ]);

        $payment->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'completed' ? now() : null,
        ]);

        $statusLabel = ucfirst($request->status);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', __('admin.payment.updated', ['status' => $statusLabel]))
            ->orJson();
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        if (!in_array($payment->status, ['pending', 'failed'])) {
            return redirect()->route('admin.payments.index')
                ->with('error', __('admin.payment.cannot_delete'))
                ->orJson();
        }

        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', __('admin.payment.deleted'))
            ->orJson();
    }
}
