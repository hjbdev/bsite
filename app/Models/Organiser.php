<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Organiser extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $appends = ['logo'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Organiser $organiser) {
            $organiser->slug = str($organiser->name)->slug();
        });

        static::updating(function (Organiser $organiser) {
            $organiser->slug = str($organiser->name)->slug();
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->performOnCollections('logo')
            ->fit(Fit::Contain, 300, 300)
            ->keepOriginalImageFormat()
            ->nonQueued();

        $this
            ->addMediaConversion('mini_preview')
            ->performOnCollections('logo')
            ->fit(Fit::Contain, 50, 50)
            ->keepOriginalImageFormat()
            ->nonQueued();
    }

    public function logo(): Attribute
    {
        $media = $this->getMedia('logo');

        return new Attribute(fn () => count($media) ? $media[0]?->getUrl('preview') : null);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }
}
