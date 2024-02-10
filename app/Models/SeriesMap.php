<?php

namespace App\Models;

use App\Enums\SeriesMapStatus;
use App\Events\SeriesMaps\SeriesMapUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SeriesMap extends Model
{
    use HasFactory;

    protected $fillable = ['team_a_score', 'team_b_score', 'series_id', 'map_id', 'start_date', 'status'];

    protected $casts = [
        'status' => SeriesMapStatus::class,
        'start_date' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::updated(function ($seriesMap) {
            broadcast(new SeriesMapUpdated($seriesMap->id, $seriesMap->series_id));
        });
    }

    public function demo(): HasOne
    {
        return $this->hasOne(Demo::class)->ofMany();
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    /** @return BelongsToMany<Player> */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withPivot([
            'kills',
            'assists',
            'deaths',
            'damage',
            'traded',
            'kast',
            'rating',
            'opening_kills',
            'opening_deaths',
            'health',
            'crosshair_share_code',
            'inspect_weapon_count',
            'kill_death_ratio',
            'bomb_defused_count',
            'bomb_planted_count',
            'armor_damage',
            'utility_damage',
            'headshot_count',
            'headshot_percent',
            'one_vs_one_count',
            'one_vs_one_won_count',
            'one_vs_one_lost_count',
            'one_vs_two_count',
            'one_vs_two_won_count',
            'one_vs_two_lost_count',
            'one_vs_three_count',
            'one_vs_three_won_count',
            'one_vs_three_lost_count',
            'one_vs_four_count',
            'one_vs_four_won_count',
            'one_vs_four_lost_count',
            'one_vs_five_count',
            'one_vs_five_won_count',
            'one_vs_five_lost_count',
            'average_kill_per_round',
            'average_death_per_round',
            'average_damage_per_round',
            'utility_damage_per_round',
            'first_kill_count',
            'first_death_count',
            'first_trade_death_count',
            'trade_death_count',
            'trade_kill_count',
            'first_trade_kill_count',
            'one_kill_count',
            'two_kill_count',
            'three_kill_count',
            'four_kill_count',
            'five_kill_count',
            'hltv_rating',
            'hltv_rating2',
        ]);
    }
}
