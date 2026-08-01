<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAmenityRequest;
use App\Http\Requests\Admin\UpdateAmenityRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Amenity::withCount('rooms');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['id', 'name', 'created_at', 'is_active'];

        $query->orderBy(
            in_array($sortField, $allowedSorts) ? $sortField : 'name',
            $sortDirection === 'desc' ? 'desc' : 'asc'
        );

        $amenities = $query->paginate(12)->withQueryString();

        return view('admin.amenities.index', compact('amenities'));
    }

    public function create(): View
    {
        return view('admin.amenities.create');
    }

    public function show(Amenity $amenity): View
    {
        $amenity->load('rooms.hotel', 'rooms.roomType');

        return view('admin.amenities.show', compact('amenity'));
    }

    public function store(StoreAmenityRequest $request): RedirectResponse|JsonResponse
    {
        Amenity::create($request->validated());

        return redirect()->route('admin.amenities.index')
            ->with('success', __('admin.amenity.created'))
            ->orJson();
    }

    public function edit(Amenity $amenity): View
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $amenity->update($request->validated());

        return redirect()->route('admin.amenities.index')
            ->with('success', __('admin.amenity.updated'));
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        if ($amenity->rooms()->count() > 0) {
            return redirect()->route('admin.amenities.index')
                ->with('error', __('admin.amenity.cannot_delete_assigned'))
                ->orJson();
        }

        $amenity->delete();

        return redirect()->route('admin.amenities.index')
            ->with('success', __('admin.amenity.deleted'))
            ->orJson();
    }

    public function toggleStatus(Amenity $amenity): RedirectResponse
    {
        $this->authorize('update', $amenity);

        $amenity->toggleStatus();
        $amenity->refresh();

        return redirect()->back()
            ->with('success', __('admin.amenity.status_updated', ['status' => $amenity->status_label]))
            ->orJson();
    }

    public function manageRooms(Amenity $amenity): View
    {
        $amenity->load('rooms.hotel');

        $hotels = Hotel::active()->orderBy('name')->get();

        $selectedRoomIds = $amenity->rooms->pluck('id')->toArray();

        return view('admin.amenities.manage-rooms', compact('amenity', 'hotels', 'selectedRoomIds'));
    }

    public function assignRooms(Request $request, Amenity $amenity): RedirectResponse
    {
        $request->validate([
            'room_ids' => ['nullable', 'array'],
            'room_ids.*' => ['exists:rooms,id'],
        ]);

        $amenity->rooms()->sync($request->room_ids ?? []);

        return redirect()->route('admin.amenities.manage-rooms', $amenity)
            ->with('success', __('admin.amenity.room_assignments_updated'))
            ->orJson();
    }

    public function getRooms(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'hotel_id' => ['required', 'exists:hotels,id'],
        ]);

        $rooms = Room::where('hotel_id', $request->hotel_id)
            ->active()
            ->with('roomType:id,name')
            ->orderBy('room_number')
            ->get()
            ->map(fn ($room) => [
                'id' => $room->id,
                'label' => $room->room_number . ' — ' . ($room->roomType?->name ?? 'Room'),
                'room_number' => $room->room_number,
                'type_name' => $room->roomType?->name,
                'floor' => $room->floor,
            ]);

        return response()->json($rooms);
    }
}
