<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'taxon_id',
        'scientific_name',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];
}
