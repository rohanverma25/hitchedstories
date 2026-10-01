<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * List the reviews.
     */
    public function index(): View
    {
        return view('admin.reviews.index', [
            'reviews' => Review::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a review.
     */
    public function create(): View
    {
        return view('admin.reviews.create', ['review' => new Review(['rating' => 5, 'is_published' => true])]);
    }

    /**
     * Store a new review.
     */
    public function store(ReviewRequest $request): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['photo', 'remove_photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('reviews', 'uploads');
        }

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review created.');
    }

    /**
     * Show the form for editing a review.
     */
    public function edit(Review $review): View
    {
        return view('admin.reviews.edit', ['review' => $review]);
    }

    /**
     * Update a review.
     */
    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['photo', 'remove_photo']);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($review);
            $data['photo'] = $request->file('photo')->store('reviews', 'uploads');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($review);
            $data['photo'] = null;
        }

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review updated.');
    }

    /**
     * Delete a review and its photo.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->deletePhoto($review);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('status', 'Review deleted.');
    }

    private function deletePhoto(Review $review): void
    {
        if ($review->photo) {
            Storage::disk('uploads')->delete($review->photo);
        }
    }
}
