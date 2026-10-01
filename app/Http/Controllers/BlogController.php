<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Average adult reading speed, in words per minute.
     */
    private const WORDS_PER_MINUTE = 200;

    /**
     * Show a published blog post.
     */
    public function show(string $slug): View
    {
        $blog = Blog::where('slug', $slug)->where('is_published', true)->firstOrFail();

        // Posts are ordered by their publish date, falling back to when they were created.
        $date = 'coalesce(published_at, created_at)';
        $current = ($blog->published_at ?? $blog->created_at)->format('Y-m-d H:i:s');

        $published = fn (): Builder => Blog::where('is_published', true)->whereKeyNot($blog->id);

        return view('blog.show', [
            'blog' => $blog,
            'readingTime' => $this->readingTime($blog->content),
            'previous' => $published()
                ->whereRaw("($date < ? or ($date = ? and id < ?))", [$current, $current, $blog->id])
                ->orderByRaw("$date desc")->orderByDesc('id')->first(),
            'next' => $published()
                ->whereRaw("($date > ? or ($date = ? and id > ?))", [$current, $current, $blog->id])
                ->orderByRaw("$date asc")->orderBy('id')->first(),
            'more' => $published()->orderByRaw("$date desc")->orderByDesc('id')->take(3)->get(),
        ]);
    }

    /**
     * Minutes needed to read the post, at least one.
     */
    private function readingTime(string $content): int
    {
        $words = str_word_count(strip_tags(html_entity_decode($content)));

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }
}
