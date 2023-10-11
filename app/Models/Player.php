<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use SteamID;

class Player extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public const CACHE_TTL = 60 * 60 * 24;

    protected $fillable = ['name', 'nationality', 'steam_id3', 'steam_id64', 'birthday', 'full_name'];

    public static function boot(): void
    {
        parent::boot();

        $convertSteamId = function (Player $player): void {
            if (! extension_loaded('gmp')) {
                return;
            }
            try {
                $steamId = new SteamID($player->steam_id64);
                $player->steam_id3 = $steamId->RenderSteam3();
            } catch (\Exception $e) {
                $player->steam_id3 = null;
                logger('Failed to convert SteamID for '.$player->name);
            }
        };

        static::creating($convertSteamId);
        static::updating($convertSteamId);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('medium')
            ->fit(Manipulations::FIT_CROP, 1000, 1000)
            ->keepOriginalImageFormat()
            ->nonQueued();

        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->keepOriginalImageFormat()
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('picture')->singleFile();
    }

    // public function logs(): HasMany
    // {
    //     return $this->hasMany(Log::class);
    // }

    public function seriesMaps(): BelongsToMany
    {
        return $this->belongsTo(SeriesMap::class)->using(PlayerSeriesMap::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot('start_date', 'end_date', 'substitute');
    }
}
