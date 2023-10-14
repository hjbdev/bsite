<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'platform',
    ];

    public function streamable(): MorphTo
    {
        return $this->morphTo();
    }
}
