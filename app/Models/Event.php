<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'slug', 'description', 'start_date', 'end_date', 'prize_pool', 'location', 'delay', 'organiser_id', 'faceit_division_id', 'faceit_championship_id'];

    protected $appends = ['logo', 'start_date_short_friendly', 'end_date_short_friendly', 'is_ongoing'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($event) {
            $event->slug = str($event->name)->slug();
        });

        static::updating(function ($event) {
            $event->slug = str($event->name)->slug();
        });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->performOnCollections('logo')
            ->fit(Fit::Crop, 300, 300)
            ->keepOriginalImageFormat()
            ->nonQueued();

        $this
            ->addMediaConversion('mini_preview')
            ->performOnCollections('logo')
            ->fit(Fit::Crop, 50, 50)
            ->keepOriginalImageFormat()
            ->nonQueued();
    }

    public function logo(): Attribute
    {
        $media = $this->getMedia('logo');

        return new Attribute(fn () => count($media) ? $media[0]?->getUrl('preview') : null);
    }

    public function getStartDateShortFriendlyAttribute(): string
    {
        return $this->start_date->format('j M');
    }

    public function getEndDateShortFriendlyAttribute(): string
    {
        return $this->end_date->format('j M');
    }

    public function getIsOngoingAttribute(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }

    public function organiser(): BelongsTo
    {
        return $this->belongsTo(Organiser::class);
    }
}
