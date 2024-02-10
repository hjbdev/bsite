<?php

namespace App\Models;

use App\Enums\DemoStatus;
use App\Jobs\Demos\ProcessDemo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Demo extends Model
{
    use HasFactory;

    protected $fillable = ['series_map_id', 'disk', 'path', 'size', 'compressed_size', 'parse_status', 'downloads'];

    protected $casts = [
        'status' => DemoStatus::class,
    ];

    public static function boot(): void
    {
        parent::boot();

        static::created(function ($demo) {
            dispatch(new ProcessDemo($demo));
        });

        static::deleting(function ($demo) {
            Storage::disk($demo->disk)->delete($demo->path);
        });
    }

    // public static function createFromFile(string $path): static
    // {
    //     // Create a new demo instance
    //     $demo = new static;

        

    //     return $demo;
    // }

    public function seriesMap(): BelongsTo
    {
        return $this->belongsTo(SeriesMap::class);
    }
}
