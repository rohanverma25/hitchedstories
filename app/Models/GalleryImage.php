<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image',
        'thumbnail',
    ];

    /**
     * Public URL of the full-size image.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('uploads/'.$this->image);
    }

    /**
     * Public URL of the thumbnail, falling back to the full image when it was
     * small enough not to need one.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail ? asset('uploads/'.$this->thumbnail) : $this->image_url;
    }
}
