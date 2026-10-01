<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function blog(array $attributes = []): Blog
    {
        return Blog::create([
            'title' => 'A Monsoon Wedding',
            'slug' => 'a-monsoon-wedding',
            'excerpt' => 'Rain, laughter and marigolds.',
            'content' => '<p>The rain arrived <strong>right on time</strong>.</p>',
            'meta_title' => 'Monsoon wedding story',
            'meta_description' => 'A wedding in the rain.',
            'is_published' => true,
            'published_at' => now(),
            ...$attributes,
        ]);
    }

    public function test_home_page_shows_sample_content_when_nothing_is_published(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Notes from')
            ->assertSee('How to plan a calm wedding morning')
            ->assertSee('Ananya &amp; Rohan', false);
    }

    public function test_home_page_shows_published_blogs_only(): void
    {
        $this->blog();
        $this->blog(['title' => 'Unfinished Draft', 'slug' => 'unfinished-draft', 'is_published' => false, 'published_at' => null]);

        $this->get('/')
            ->assertOk()
            ->assertSee('A Monsoon Wedding')
            ->assertSee('Rain, laughter and marigolds.')
            ->assertSee(route('blog.show', 'a-monsoon-wedding'))
            ->assertDontSee('Unfinished Draft')
            ->assertDontSee('How to plan a calm wedding morning');
    }

    public function test_blogs_are_paginated_three_per_page_inside_the_journal_section(): void
    {
        foreach (range(1, 7) as $number) {
            $this->blog([
                'title' => "Story Number {$number}",
                'slug' => "story-{$number}",
                'published_at' => now()->subDays(10 - $number),
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Story Number 7', 'Story Number 6', 'Story Number 5'])
            ->assertDontSee('Story Number 4')
            ->assertSee('journal=2#journal', false)
            ->assertSee('journal=3#journal', false);

        $this->get('/?journal=2')
            ->assertOk()
            ->assertSeeInOrder(['Story Number 4', 'Story Number 3', 'Story Number 2'])
            ->assertDontSee('Story Number 5')
            ->assertDontSee('Story Number 1');

        $this->get('/?journal=3')->assertOk()->assertSee('Story Number 1')->assertDontSee('Story Number 2');

        // A page past the end shows no posts, not the sample cards.
        $this->get('/?journal=9')->assertOk()->assertDontSee('How to plan a calm wedding morning');
    }

    public function test_pagination_is_hidden_when_there_is_only_one_page(): void
    {
        $this->blog();

        $this->get('/')->assertOk()->assertDontSee('aria-label="Journal pages"', false);
    }

    public function test_home_page_shows_published_reviews_only(): void
    {
        Review::create(['name' => 'Priya Sharma', 'designation' => 'Bride, Mumbai', 'rating' => 4, 'review' => 'Everything was perfect.', 'is_published' => true]);
        Review::create(['name' => 'Hidden Person', 'rating' => 5, 'review' => 'Not visible.', 'is_published' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Priya Sharma')
            ->assertSee('Everything was perfect.')
            ->assertSee('4 out of 5 stars')
            ->assertDontSee('Hidden Person')
            ->assertDontSee('Meera &amp; Kabir', false);
    }

    public function test_published_blog_has_its_own_page_with_meta(): void
    {
        $this->blog();

        $this->get('/blog/a-monsoon-wedding')
            ->assertOk()
            ->assertSee('A Monsoon Wedding')
            ->assertSee('<strong>right on time</strong>', false)
            ->assertSee('<title>Monsoon wedding story', false)
            ->assertSee('content="A wedding in the rain."', false);
    }

    public function test_draft_or_missing_blog_page_is_not_found(): void
    {
        $this->blog(['is_published' => false, 'published_at' => null]);

        $this->get('/blog/a-monsoon-wedding')->assertNotFound();
        $this->get('/blog/does-not-exist')->assertNotFound();
    }
}
