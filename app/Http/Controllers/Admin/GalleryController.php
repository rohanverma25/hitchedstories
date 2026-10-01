<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryUploadRequest;
use App\Models\GalleryImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private ImageService $images)
    {
    }

    /**
     * Show the gallery.
     */
    public function index(): View
    {
        return view('admin.gallery.index', [
            'images' => GalleryImage::latest('id')->paginate(24),
        ]);
    }

    /**
     * Upload one or more images to the gallery.
     */
    public function store(GalleryUploadRequest $request): RedirectResponse
    {
        $files = $request->file('images');

        foreach ($files as $file) {
            GalleryImage::create($this->images->store($file, 'gallery'));
        }

        $count = count($files);

        return redirect()->route('admin.gallery.index')
            ->with('status', $count.' '.Str::plural('image', $count).' uploaded.');
    }

    /**
     * Delete a single image.
     */
    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        $this->remove($galleryImage);

        return back()->with('status', 'Image deleted.');
    }

    /**
     * Delete the selected images.
     */
    public function destroyMany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Select at least one image to delete.',
        ]);

        $selected = GalleryImage::whereKey($validated['ids'])->get();
        $selected->each(fn (GalleryImage $image) => $this->remove($image));

        $count = $selected->count();

        return redirect()->route('admin.gallery.index')
            ->with('status', $count.' '.Str::plural('image', $count).' deleted.');
    }

    private function remove(GalleryImage $image): void
    {
        $this->images->delete($image->image, $image->thumbnail);
        $image->delete();
    }
}
