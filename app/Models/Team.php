<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function seriesA(): HasMany
    {
        return $this->hasMany(Series::class, 'team_a_id');
    }

    public function seriesB(): HasMany
    {
        return $this->hasMany(Series::class, 'team_b_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withPivot('start_date', 'end_date', 'substitute');
    }
}