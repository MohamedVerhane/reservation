<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $query = Room::with(['hotel', 'roomType'])->withCount('reservations');

        if (request('search')) {
            $term = '%' . addslashes(request('search')) . '%';
            $query->where(function ($q) use ($term): void {
                $q->where('room_number', 'LIKE', $term)
                  ->orWhereHas('hotel', fn ($hq) => $hq->where('name', 'LIKE', $term))
                  ->orWhereHas('roomType', fn ($tq) => $tq->where('name', 'LIKE', $term));
            });
        }

        if (request('hotel_id')) {
            $query->where('hotel_id', request('hotel_id'));
        }

        if (request('type_id')) {
            $query->where('room_type_id', request('type_id'));
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('status', request('status'));
        }

        if (request('floor') !== null && request('floor') !== '') {
            $query->where('floor', request('floor'));
        }

        $sortField = request('sort', 'id');
        $sortDir = request('dir', 'desc');
        $allowed = ['room_number', 'floor', 'status', 'created_at', 'id'];

        $query->orderBy(in_array($sortField, $allowed) ? $sortField : 'id', $sortDir === 'asc' ? 'asc' : 'desc');

        $rooms = $query->paginate(12)->withQueryString();
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');
        $roomTypes = RoomType::active()->orderBy('name')->pluck('name', 'id');
        $floors = Room::distinct()->whereNotNull('floor')->orderBy('floor')->pluck('floor');

        return view('admin.rooms.index', compact('rooms', 'hotels', 'roomTypes', 'floors'));
    }

    public function create(): View
    {
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');
        $roomTypes = RoomType::active()->orderBy('name')->pluck('name', 'id');
        $roomTypesByHotel = RoomType::active()->orderBy('name')
            ->get(['id', 'hotel_id', 'name'])
            ->groupBy('hotel_id')
            ->map(fn ($group) => $group->map(fn (RoomType $rt) => [
                'id' => (string) $rt->id,
                'name' => $rt->name,
            ])->values());
        $amenities = Amenity::alphabetical()->get();

        return view('admin.rooms.create', compact('hotels', 'roomTypes', 'roomTypesByHotel', 'amenities'));
    }

    public function store(StoreRoomRequest $request): RedirectResponse|JsonResponse
    {
        $room = Room::create($request->validated());

        if ($request->has('amenities')) {
            $room->amenities()->sync($request->input('amenities', []));
        }

        $this->uploadImages($room, $request);

        return redirect()->route('admin.rooms.index')
            ->with('success', __('admin.room.created'))
            ->orJson();
    }

    public function show(Room $room): View
    {
        $this->authorize('view', $room);

        $room->load(['hotel', 'roomType', 'amenities', 'images', 'reservations' => fn ($q) => $q->latest()->limit(5)])
            ->loadCount(['reservations', 'images']);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $this->authorize('update', $room);

        $room->load('amenities');
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');
        $roomTypes = RoomType::active()->orderBy('name')->pluck('name', 'id');
        $roomTypesByHotel = RoomType::active()->orderBy('name')
            ->get(['id', 'hotel_id', 'name'])
            ->groupBy('hotel_id')
            ->map(fn ($group) => $group->map(fn (RoomType $rt) => [
                'id' => (string) $rt->id,
                'name' => $rt->name,
            ])->values());
        $amenities = Amenity::alphabetical()->get();

        return view('admin.rooms.edit', compact('room', 'hotels', 'roomTypes', 'roomTypesByHotel', 'amenities'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $room->update($request->validated());
        $room->amenities()->sync($request->input('amenities', []));

        $this->uploadImages($room, $request);

        return redirect()->route('admin.rooms.index')
            ->with('success', __('admin.room.updated'));
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        foreach ($room->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', __('admin.room.deleted'))
            ->orJson();
    }

    public function toggleStatus(Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $statuses = ['available', 'occupied', 'maintenance', 'out_of_order'];
        $currentIndex = array_search($room->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $room->update(['status' => $statuses[$nextIndex]]);

        return redirect()->back()
            ->with('success', __('admin.room.status_updated', ['status' => $room->status_label]))
            ->orJson();
    }

    public function uploadImage(Request $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('image')->store('rooms', 'public');
        $maxOrder = $room->images()->max('sort_order') ?? 0;

        $room->images()->create([
            'path' => $path,
            'alt_text' => $request->input('alt_text'),
            'sort_order' => $maxOrder + 1,
            'is_primary' => $room->images()->count() === 0,
        ]);

        return redirect()->back()->with('success', __('admin.room.image_uploaded'))
            ->orJson();
    }

    public function deleteImage(Room $room, RoomImage $image): RedirectResponse
    {
        $this->authorize('update', $room);

        if ($image->is_primary && $room->images()->count() > 1) {
            $room->images()->where('id', '!=', $image->id)->first()?->update(['is_primary' => true]);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->back()->with('success', __('admin.room.image_deleted'))
            ->orJson();
    }

    public function setPrimary(Room $room, RoomImage $image): RedirectResponse
    {
        $this->authorize('update', $room);

        $room->images()->where('is_primary', true)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return redirect()->back()->with('success', __('admin.room.primary_image_updated'))
            ->orJson();
    }

    private function uploadImages(Room $room, StoreRoomRequest|UpdateRoomRequest $request): void
    {
        if ($request->hasFile('images')) {
            $maxOrder = $room->images()->max('sort_order') ?? 0;

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('rooms', 'public');
                $room->images()->create([
                    'path' => $path,
                    'sort_order' => $maxOrder + $index + 1,
                    'is_primary' => $room->images()->count() === 0 && $index === 0,
                ]);
            }
        }
    }
}
