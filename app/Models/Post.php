<?php

// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // ← add this
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ add this relation so ->with(['user','media']) works
    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    // (rest stays exactly as you have)
    protected static function booted()
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->title);
            }
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('title')) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });
    }

    protected static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
