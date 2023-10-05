<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Team extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name'];

    protected $appends = ['logo'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();

        $this
            ->addMediaConversion('mini_preview')
            ->performOnCollections('logo')
            ->fit(Manipulations::FIT_CROP, 50, 50)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function logo(): Attribute
    {
        $media = $this->getMedia('logo');
        return new Attribute(fn() => count($media) ? $media[0]?->getUrl('preview') : null);
    }

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

    public function vetos(): HasMany
    {
        return $this->hasMany(Veto::class);
    }
}
