<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
class Blog extends Model
{
    use HasUniqueSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'thumbnail',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Public URL of the full-size image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('uploads/'.$this->image) : null;
    }

    /**
     * Public URL of the thumbnail, falling back to the full image when it was
     * small enough not to need one.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('uploads/'.$this->thumbnail) : $this->image_url;
    }
}
