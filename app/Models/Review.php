<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'designation',
        'rating',
        'review',
        'photo',
        'is_published',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Public URL of the reviewer's photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('uploads/'.$this->photo) : null;
    }
}
