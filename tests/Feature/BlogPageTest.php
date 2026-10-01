<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPageTest extends TestCase
{
    use RefreshDatabase;

    private function blog(int $number, array $attributes = []): Blog
    {
        return Blog::create([
            'title' => "Story {$number}",
            'slug' => "story-{$number}",
            'excerpt' => "Excerpt {$number}",
            'content' => '<p>Some words for story '.$number.'.</p>',
            'is_published' => true,
            'published_at' => now()->subDays(20 - $number),
            ...$attributes,
        ]);
    }

    public function test_page_shows_breadcrumb_date_reading_time_and_byline(): void
    {
        $this->blog(1, ['published_at' => '2026-03-14 10:00:00']);

        $this->get('/blog/story-1')
            ->assertOk()
            ->assertSee('aria-label="Breadcrumb"', false)
            ->assertSee('14 March 2026')
            ->assertSee('1 min read')
            ->assertSee('By Hitched')
            ->assertSee('Excerpt 1');
    }

    public function test_reading_time_grows_with_the_length_of_the_post(): void
    {
        $this->blog(1, ['content' => '<p>'.str_repeat('word ', 650).'</p>']);

        $this->get('/blog/story-1')->assertSee('4 min read');
    }

    public function test_social_preview_and_search_metadata_are_present(): void
    {
        $this->blog(1, ['image' => 'blogs/cover.jpg', 'meta_title' => 'A better title', 'meta_description' => 'A short summary.']);

        $this->get('/blog/story-1')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('blog.show', 'story-1').'">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:title" content="A better title">', false)
            ->assertSee('<meta property="og:image" content="'.asset('uploads/blogs/cover.jpg').'">', false)
            ->assertSee('summary_large_image', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('"headline":"Story 1"', false);
    }

    public function test_post_without_an_image_uses_a_small_twitter_card(): void
    {
        $this->blog(1);

        $this->get('/blog/story-1')->assertOk()->assertSee('content="summary"', false)->assertDontSee('og:image', false);
    }

    public function test_share_links_point_at_the_post(): void
    {
        $this->blog(1);
        $encoded = urlencode(route('blog.show', 'story-1'));

        $this->get('/blog/story-1')
            ->assertOk()
            ->assertSee('https://www.facebook.com/sharer/sharer.php?u='.$encoded, false)
            ->assertSee('https://wa.me/?text=Story%201%20'.$encoded, false)
            ->assertSee('data-copy-link="'.route('blog.show', 'story-1').'"', false);
    }

    public function test_previous_and_next_stories_follow_the_publish_date(): void
    {
        $this->blog(1);
        $this->blog(2);
        $this->blog(3);
        $this->blog(4, ['is_published' => false]);

        $this->get('/blog/story-2')
            ->assertOk()
            ->assertSee('rel="prev"', false)
            ->assertSee('href="'.route('blog.show', 'story-1').'" rel="prev"', false)
            ->assertSee('href="'.route('blog.show', 'story-3').'" rel="next"', false);

        // The newest post has nothing after it, and the oldest nothing before it.
        $this->get('/blog/story-3')->assertOk()->assertDontSee('rel="next"', false)->assertSee('rel="prev"', false);
        $this->get('/blog/story-1')->assertOk()->assertDontSee('rel="prev"', false)->assertSee('rel="next"', false);
    }

    public function test_single_post_has_no_navigation_or_related_stories(): void
    {
        $this->blog(1);

        $this->get('/blog/story-1')
            ->assertOk()
            ->assertDontSee('aria-label="More stories"', false)
            ->assertDontSee('Keep reading');
    }

    public function test_related_stories_exclude_the_current_post_and_drafts(): void
    {
        $this->blog(1);
        $this->blog(2);
        $this->blog(3);
        $this->blog(4);
        $this->blog(5, ['title' => 'Secret Draft', 'slug' => 'secret-draft', 'is_published' => false]);

        $this->get('/blog/story-4')
            ->assertOk()
            ->assertSee('Keep reading')
            ->assertSee('href="'.route('blog.show', 'story-3').'"', false)
            ->assertSee('href="'.route('blog.show', 'story-2').'"', false)
            ->assertSee('href="'.route('blog.show', 'story-1').'"', false)
            ->assertDontSee('Secret Draft');
    }

    public function test_page_ends_with_a_call_to_action_to_the_contact_form(): void
    {
        $this->blog(1);

        $this->get('/blog/story-1')->assertOk()->assertSee(route('home').'#contact', false)->assertSee('Planning your');
    }
}
