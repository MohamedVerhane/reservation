<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Reservation;
use App\Models\Review;
use App\Notifications\BookingCancelled;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        // Optimized: Use single query with conditional counting
        $activeCount = Reservation::byUser($userId)->active()->count();
        $upcomingCount = Reservation::byUser($userId)->upcoming()->count();
        $reviewsCount = Review::byUser($userId)->count();
        $favoritesCount = Favorite::byUser($userId)->count();

        // Eager load relationships with optimized queries
        $upcomingReservations = Reservation::byUser($userId)
            ->upcoming()
            ->with(['hotel:id,name,slug,cover_image', 'room:id,hotel_id,room_type_id,room_number,status', 'room.roomType:id,name,base_price'])
            ->latest('check_in')
            ->limit(3)
            ->get();

        $recentReviews = Review::byUser($userId)
            ->ordered()
            ->with('hotel:id,name,slug,cover_image')
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact(
            'activeCount',
            'upcomingCount',
            'reviewsCount',
            'favoritesCount',
            'upcomingReservations',
            'recentReviews',
        ));
    }

    public function reservations(Request $request): View
    {
        $userId = Auth::id();

        $query = Reservation::byUser($userId)
            ->with([
                'hotel:id,name,slug,cover_image,city',
                'room:id,hotel_id,room_type_id,room_number,status',
                'room.roomType:id,name,base_price',
                'payments:id,reservation_id,amount,status,method,paid_at',
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reservations = $query->latest()->paginate(10)->withQueryString();

        return view('customer.reservations', compact('reservations'));
    }

    public function profile(): View
    {
        $user = Auth::user();
        $reviewsCount = Review::byUser($user->id)->count();
        $reservationsCount = Reservation::where('user_id', $user->id)->count();

        return view('customer.profile', compact('user', 'reviewsCount', 'reservationsCount'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|max:20',
        ]);

        Auth::user()->update($validated);

        return redirect()->back()->with('success', __('admin.profile.updated'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', __('auth.cd_password_updated'));
    }

    public function reviews(): View
    {
        $userId = Auth::id();

        $reviews = Review::byUser($userId)
            ->ordered()
            ->with('hotel:id,name,slug,cover_image')
            ->paginate(10);

        return view('customer.reviews', compact('reviews'));
    }

    public function favorites(): View
    {
        $userId = Auth::id();

        $favorites = Favorite::byUser($userId)
            ->with(['hotel' => function ($query) {
                $query->withCount('reviews', 'rooms')
                    ->select('id', 'name', 'slug', 'cover_image', 'city', 'star_rating');
            }])
            ->latest()
            ->paginate(12);

        return view('customer.favorites', compact('favorites'));
    }

    public function toggleFavorite(Request $request): JsonResponse
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);

        $userId = Auth::id();
        $hotelId = $request->input('hotel_id');

        $isFavorited = Favorite::toggle($userId, $hotelId);
        $favoritesCount = Favorite::where('hotel_id', $hotelId)->count();

        return response()->json([
            'is_favorited' => $isFavorited,
            'favorites_count' => $favoritesCount,
        ]);
    }

    public function invoices(): View
    {
        $userId = Auth::id();

        $reservations = Reservation::byUser($userId)
            ->whereHas('payments', function ($query) {
                $query->completed();
            })
            ->with([
                'hotel:id,name,slug,cover_image,city',
                'room:id,hotel_id,room_type_id,room_number,status',
                'room.roomType:id,name,base_price',
                'payments' => function ($query) {
                    $query->completed()->select('id', 'reservation_id', 'amount', 'method', 'status', 'paid_at', 'transaction_id');
                },
            ])
            ->latest()
            ->paginate(10);

        return view('customer.invoices', compact('reservations'));
    }

    public function history(): View
    {
        $userId = Auth::id();

        $reservations = Reservation::byUser($userId)
            ->where(function ($query) {
                $query->where('status', 'checked_out')
                    ->orWhere('status', 'cancelled');
            })
            ->with([
                'hotel:id,name,slug,cover_image,city',
                'room:id,hotel_id,room_type_id,room_number,status',
                'room.roomType:id,name,base_price',
                'payments:id,reservation_id,amount,status,method,paid_at',
            ])
            ->latest()
            ->paginate(10);

        return view('customer.history', compact('reservations'));
    }

    public function cancelReservation(Reservation $reservation): RedirectResponse
    {
        $userId = Auth::id();

        abort_unless($reservation->user_id === $userId, 403);
        abort_unless($reservation->canBeCancelled(), 422);

        $reservation->cancel();
        $reservation->load(['hotel:id,name,slug', 'room:id,room_number']);
        Auth::user()->notify(new BookingCancelled($reservation, __('admin.reservation.cancelled_by_guest')));

        return redirect()->route('customer.reservations')->with('success', __('auth.cd_cancel_reservation'));
    }

    public function destroyReview(Review $review): RedirectResponse
    {
        $userId = Auth::id();

        abort_unless($review->user_id === $userId, 403);

        $review->delete();

        return redirect()->route('customer.reviews')->with('success', __('auth.cd_delete_review'));
    }
}
