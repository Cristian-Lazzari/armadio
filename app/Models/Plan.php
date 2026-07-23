<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'state'];

    protected $casts = [
        'state' => 'array',
    ];

    /** Nome riservato al piano di autosalvataggio corrente. */
    public const AUTO = '_auto';
}
