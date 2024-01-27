<?php

namespace App\Models;

use App\Jobs\SocialPosts\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'platform',
        'platform_post_id',
        'platform_post_url',
        'generator_url',
        'type',
        'link_to_post',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function (SocialPost $model) {
            dispatch(new Post($model));
        });
    }

    public function postable(): MorphTo
    {
        return $this->morphTo();
    }
}
