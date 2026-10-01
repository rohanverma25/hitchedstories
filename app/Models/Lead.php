<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    /**
     * Lead statuses, as value => label.
     */
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'booked' => 'Booked',
        'closed' => 'Closed',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_date',
        'venue',
        'message',
        'status',
        'notes',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
