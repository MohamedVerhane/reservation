<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\Hotel;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $query = Gallery::with('hotel')->withCount('images');

        if (request('search')) {
            $term = '%' . addslashes(request('search')) . '%';
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'LIKE', $term)
                  ->orWhere('description', 'LIKE', $term)
                  ->orWhereHas('hotel', fn ($hq) => $hq->where('name', 'LIKE', $term));
            });
        }

        if (request('hotel_id')) {
            $query->where('hotel_id', request('hotel_id'));
        }

        $galleries = $query->ordered()->paginate(12)->withQueryString();
        $hotels = Hotel::orderBy('name')->pluck('name', 'id');

        return view('admin.galleries.index', compact('galleries', 'hotels'));
    }

    public function create(): View
    {
        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.galleries.create', compact('hotels'));
    }

    public function store(StoreGalleryRequest $request): RedirectResponse|JsonResponse
    {
        $gallery = Gallery::create($request->validated());

        $this->handleImageUpload($gallery, $request);

        return redirect()->route('admin.galleries.show', $gallery)
            ->with('success', __('admin.gallery.created'))
            ->orJson();
    }

    public function show(Gallery $gallery): View
    {
        $this->authorize('view', $gallery);

        $gallery->load(['hotel', 'images' => fn ($q) => $q->ordered()])
            ->loadCount('images');

        return view('admin.galleries.show', compact('gallery'));
    }

    public function edit(Gallery $gallery): View
    {
        $this->authorize('update', $gallery);

        $hotels = Hotel::active()->orderBy('name')->pluck('name', 'id');

        return view('admin.galleries.edit', compact('gallery', 'hotels'));
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->authorize('update', $gallery);

        $gallery->update($request->validated());

        return redirect()->route('admin.galleries.show', $gallery)
            ->with('success', __('admin.gallery.updated'));
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);

        foreach ($gallery->images as $image) {
            $image->deleteFromStorage();
        }

        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', __('admin.gallery.deleted'));
    }

    public function uploadImages(Request $request, Gallery $gallery): RedirectResponse
    {
        $this->authorize('update', $gallery);

        $request->validate([
            'images' => ['required', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->handleImageUpload($gallery, $request);

        return redirect()->back()->with('success', __('admin.gallery.images_uploaded'));
    }

    public function deleteImage(Gallery $gallery, Image $image): RedirectResponse
    {
        $this->authorize('update', $gallery);

        abort_unless($image->gallery_id === $gallery->id, 403);

        $image->deleteFromStorage();

        return redirect()->back()->with('success', __('admin.gallery.image_deleted'));
    }

    private function handleImageUpload(Gallery $gallery, Request $request): void
    {
        if ($request->hasFile('images')) {
            $maxOrder = $gallery->images()->max('sort_order') ?? 0;

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store("galleries/{$gallery->id}", 'public');
                $gallery->images()->create([
                    'path' => $path,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }
    }
}
