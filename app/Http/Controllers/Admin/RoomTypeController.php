<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomTypeRequest;
use App\Http\Requests\Admin\UpdateRoomTypeRequest;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    public function index(): View
    {
        $query = RoomType::with('hotel')->withCount('rooms');

        if (request('search')) {
            $term = '%' . addslashes(request('search')) . '%';
            $query->where(function ($q) use ($term): void {
                $q->where('name', 'LIKE', $term)
                  ->orWhereHas('hotel', fn ($hq) => $hq->where('name', 'LIKE', $term));
            });
        }

        if (request('hotel_id')) {
            $query->where('hotel_id', request('hotel_id'));
        }

        if (request('status') !== null && request('status') !== '') {
            $query->where('is_active', (bool) request('status'));
        }

        $sortField = request('sort', 'id');
        $sortDir = request('dir', 'desc');
        $allowed = ['name', 'base_price', 'max_guests', 'created_at', 'id'];

        $query->orderBy(in_array($sortField, $allowed) ? $sortField : 'id', $sortDir === 'asc' ? 'asc' : 'desc');

        $roomTypes = $query->paginate(12)->withQueryString();
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.room-types.index', compact('roomTypes', 'hotels'));
    }

    public function create(): View
    {
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.room-types.create', compact('hotels'));
    }

    public function store(StoreRoomTypeRequest $request): RedirectResponse|JsonResponse
    {
        RoomType::create($request->validated());

        return redirect()->route('admin.room-types.index')
            ->with('success', __('admin.room_type.created'))
            ->orJson();
    }

    public function show(RoomType $roomType): View
    {
        $this->authorize('view', $roomType);

        $roomType->load(['hotel', 'rooms'])
            ->loadCount('rooms');

        return view('admin.room-types.show', compact('roomType'));
    }

    public function edit(RoomType $roomType): View
    {
        $this->authorize('update', $roomType);

        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.room-types.edit', compact('roomType', 'hotels'));
    }

    public function update(UpdateRoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $this->authorize('update', $roomType);

        $roomType->update($request->validated());

        return redirect()->route('admin.room-types.index')
            ->with('success', __('admin.room_type.updated'));
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $this->authorize('delete', $roomType);

        $roomType->delete();

        return redirect()->route('admin.room-types.index')
            ->with('success', __('admin.room_type.deleted'))
            ->orJson();
    }

    public function toggleStatus(RoomType $roomType): RedirectResponse
    {
        $this->authorize('update', $roomType);

        $roomType->update(['is_active' => !$roomType->is_active]);
        $roomType->refresh();

        return redirect()->back()
            ->with('success', __('admin.room_type.status_updated', ['status' => $roomType->status_label]))
            ->orJson();
    }
}
