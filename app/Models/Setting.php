<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * All settings as a key => value array.
     *
     * @return array<string, ?string>
     */
    public static function values(): array
    {
        return static::pluck('value', 'key')->all();
    }

    /**
     * Get a single setting.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Save several settings at once.
     *
     * @param  array<string, ?string>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Public URL of the uploaded logo, if there is one.
     */
    public static function logoUrl(): ?string
    {
        $logo = static::get('logo');

        return $logo ? asset('uploads/'.$logo) : null;
    }
}
