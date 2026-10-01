<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogRequest;
use App\Models\Blog;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(private ImageService $images)
    {
    }

    /**
     * List the blogs.
     */
    public function index(): View
    {
        return view('admin.blogs.index', [
            'blogs' => Blog::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a blog.
     */
    public function create(): View
    {
        return view('admin.blogs.create', ['blog' => new Blog]);
    }

    /**
     * Store a new blog.
     */
    public function store(BlogRequest $request): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $data = [...$data, ...$this->images->store($request->file('image'), 'blogs')];
        }

        $data['published_at'] = $data['is_published'] ? now() : null;

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('status', 'Blog created.');
    }

    /**
     * Show the form for editing a blog.
     */
    public function edit(Blog $blog): View
    {
        return view('admin.blogs.edit', ['blog' => $blog]);
    }

    /**
     * Update a blog.
     */
    public function update(BlogRequest $request, Blog $blog): RedirectResponse
    {
        $data = Arr::except($request->validated(), ['image', 'remove_image']);

        if ($request->hasFile('image')) {
            $this->images->delete($blog->image, $blog->thumbnail);
            $data = [...$data, ...$this->images->store($request->file('image'), 'blogs')];
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($blog->image, $blog->thumbnail);
            $data = [...$data, 'image' => null, 'thumbnail' => null];
        }

        $data['published_at'] = $data['is_published'] ? ($blog->published_at ?? now()) : null;

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('status', 'Blog updated.');
    }

    /**
     * Delete a blog and its images.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        $this->images->delete($blog->image, $blog->thumbnail);
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('status', 'Blog deleted.');
    }
}
