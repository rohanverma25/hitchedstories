<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\GalleryImage;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Blogs shown per page in the Journal section (one row of three).
     */
    private const BLOGS_PER_PAGE = 3;

    /**
     * Show the single-page site.
     */
    public function __invoke(): View
    {
        // Paginated inside the Journal section: the page links reload the home
        // page and jump straight back to #journal.
        $blogs = Blog::where('is_published', true)
            ->latest('published_at')
            ->latest('id')
            ->paginate(self::BLOGS_PER_PAGE, ['*'], 'journal')
            ->fragment('journal');

        return view('home', [
            'services' => $this->services(),
            'gallery' => $this->gallery(),
            'reviews' => $this->reviews(),
            'posts' => $this->posts($blogs),
            'blogs' => $blogs,
        ]);
    }

    /**
     * Published services in the admin's order, or sample ones until some are added.
     *
     * @return list<array{title: string, text: string, image: string}>
     */
    private function services(): array
    {
        $services = Service::where('is_published', true)->orderBy('sort_order')->orderBy('title')->get();

        if ($services->isEmpty()) {
            return [
                ['image' => asset('images/site/portfolio-weddings.jpg'), 'title' => 'Wedding Photography', 'text' => 'Full-day coverage by two photographers, from the quiet morning to the last dance, delivered as a hand-edited gallery.'],
                ['image' => asset('images/site/hero.jpg'), 'title' => 'Wedding Films', 'text' => 'A cinematic film of your day with natural sound, vows and speeches, cut to music that suits the two of you.'],
                ['image' => asset('images/site/portfolio-couples.jpg'), 'title' => 'Pre-Wedding Shoots', 'text' => 'A relaxed session somewhere that matters to you. No stiff poses, only the two of you being yourselves.'],
                ['image' => asset('images/site/about.jpg'), 'title' => 'Bridal Portraits', 'text' => 'Unhurried portraits of the bride in her finest, made with soft light and plenty of time.'],
            ];
        }

        $fallbacks = ['portfolio-weddings.jpg', 'portfolio-couples.jpg', 'about.jpg', 'hero.jpg'];

        return $services->values()->map(fn (Service $service, int $index) => [
            'image' => $service->thumbnail_url ?: asset('images/site/'.$fallbacks[$index % count($fallbacks)]),
            'title' => $service->title,
            'text' => $service->short_description ?: Str::limit(trim(html_entity_decode(strip_tags((string) $service->description))), 160),
        ])->all();
    }

    /**
     * Gallery photos, newest first, or the bundled sample photos until some are uploaded.
     *
     * @return list<array{src: string, alt: string}>
     */
    private function gallery(): array
    {
        $images = GalleryImage::latest('id')->take(30)->get();

        if ($images->isEmpty()) {
            return array_map(fn (int $number) => [
                'src' => asset('images/site/gallery-'.str_pad($number, 2, '0', STR_PAD_LEFT).'.jpg'),
                'alt' => 'Wedding photograph '.$number,
            ], range(1, 10));
        }

        return $images->values()->map(fn (GalleryImage $image, int $index) => [
            'src' => $image->image_url,
            'alt' => 'Wedding photograph '.($index + 1),
        ])->all();
    }

    /**
     * Published reviews for the slider, or sample ones until some are added in the admin panel.
     *
     * @return list<array{text: string, name: string, designation: ?string, rating: int, photo: ?string}>
     */
    private function reviews(): array
    {
        $reviews = Review::where('is_published', true)->latest()->take(8)->get();

        if ($reviews->isEmpty()) {
            return [
                ['text' => 'Looking through our gallery felt like living the day again. They caught moments we did not even know had happened.', 'name' => 'Ananya & Rohan', 'designation' => 'Udaipur', 'rating' => 5, 'photo' => null],
                ['text' => 'Calm, kind and almost invisible. Our families still talk about how easy the whole team was to be around.', 'name' => 'Meera & Kabir', 'designation' => 'Jaipur', 'rating' => 5, 'photo' => null],
                ['text' => 'The film made both our mothers cry, in the best way. It is the one thing from the wedding we will keep forever.', 'name' => 'Simran & Arjun', 'designation' => 'Goa', 'rating' => 5, 'photo' => null],
            ];
        }

        return $reviews->map(fn (Review $review) => [
            'text' => $review->review,
            'name' => $review->name,
            'designation' => $review->designation,
            'rating' => $review->rating,
            'photo' => $review->photo_url,
        ])->all();
    }

    /**
     * The current page of published blogs, or sample cards until some are published.
     *
     * @return list<array{title: string, excerpt: string, image: ?string, date: string, url: string}>
     */
    private function posts(LengthAwarePaginator $blogs): array
    {
        if ($blogs->total() === 0) {
            return [
                ['title' => 'How to plan a calm wedding morning', 'excerpt' => 'A few small choices that keep the hours before the ceremony slow, light and full of good photographs.', 'image' => asset('images/site/gallery-02.jpg'), 'date' => 'Planning', 'url' => '#journal'],
                ['title' => 'Golden hour, and why we chase it', 'excerpt' => 'Twenty minutes of soft evening light can change a whole gallery. Here is how to make room for it.', 'image' => asset('images/site/gallery-06.jpg'), 'date' => 'Photography', 'url' => '#journal'],
                ['title' => 'The details worth remembering', 'excerpt' => 'Rings, flowers, handwritten notes: the little things you chose say more than you might think.', 'image' => asset('images/site/gallery-09.jpg'), 'date' => 'Inspiration', 'url' => '#journal'],
            ];
        }

        return collect($blogs->items())->map(fn (Blog $blog) => [
            'title' => $blog->title,
            'excerpt' => $blog->excerpt ?: Str::limit(trim(html_entity_decode(strip_tags($blog->content))), 140),
            'image' => $blog->thumbnail_url,
            'date' => ($blog->published_at ?? $blog->created_at)->format('j F Y'),
            'url' => route('blog.show', $blog->slug),
        ])->all();
    }
}
