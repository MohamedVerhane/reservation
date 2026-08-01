<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelRequest;
use App\Http\Requests\Admin\UpdateHotelRequest;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(): View
    {
        $query = Hotel::with('user')->withCount(['rooms', 'reservations', 'reviews']);

        if (request('search')) {
            $query->search(request('search'));
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('is_active', (bool) request('status'));
        }

        if (request('trashed') === '1') {
            $query->onlyTrashed();
        }

        $hotels = $query->latest()->paginate(10)->withQueryString();

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        return view('admin.hotels.create');
    }

    public function store(StoreHotelRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('hotels', 'public');
        }

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', __('admin.hotel.created'))
            ->orJson();
    }

    public function show(Hotel $hotel): View
    {
        $this->authorize('view', $hotel);

        $hotel->load(['user', 'rooms.roomType', 'roomTypes', 'reviews.user', 'galleries.images'])
            ->loadCount(['rooms', 'reservations', 'reviews']);

        return view('admin.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        $this->authorize('update', $hotel);

        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('update', $hotel);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($hotel->cover_image) {
                Storage::disk('public')->delete($hotel->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')
                ->store('hotels', 'public');
        }

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', __('admin.hotel.updated'));
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $this->authorize('delete', $hotel);

        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', __('admin.hotel.deleted'));
    }

    public function restore(int $id): RedirectResponse
    {
        $hotel = Hotel::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $hotel);

        $hotel->restore();

        return redirect()->route('admin.hotels.index', ['trashed' => '1'])
            ->with('success', __('admin.hotel.restored'));
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $hotel = Hotel::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $hotel);

        if ($hotel->cover_image) {
            Storage::disk('public')->delete($hotel->cover_image);
        }

        $hotel->forceDelete();

        return redirect()->route('admin.hotels.index', ['trashed' => '1'])
            ->with('success', __('admin.hotel.permanently_deleted'));
    }

    public function toggleStatus(Hotel $hotel): RedirectResponse
    {
        $this->authorize('update', $hotel);

        $hotel->update(['is_active' => !$hotel->is_active]);
        $hotel->refresh();

        return redirect()->back()
            ->with('success', __('admin.hotel.status_updated', ['status' => $hotel->status_label]));
    }
}
