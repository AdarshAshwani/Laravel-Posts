<?php

// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relation so ->with(['user','media']) works
    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    /**
     * Scope: search by description and title.
     * - Tries MySQL FULLTEXT (boolean mode) if available.
     * - Falls back to LIKE/ILIKE for other drivers or if FULLTEXT index missing.
     *
     * Usage:
     * Post::with(['user','media'])->search($request->query('q'))->latest()->paginate(10);
     */
    public function scopeSearch($query, ?string $q)
    {
        $q = trim((string) $q);
        if ($q === '') return $query;

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            try {
                // requires a FULLTEXT index on (title, description)
                // see migration suggestion earlier
                return $query->whereFullText(['title', 'description'], $q, ['mode' => 'boolean']);
            } catch (\Throwable $e) {
                // continue to fallback below
            }
        }

        $op = $driver === 'pgsql' ? 'ilike' : 'like';
        return $query->where(function ($qb) use ($q, $op) {
            $qb->where('description', $op, "%{$q}%")
               ->orWhere('title', $op, "%{$q}%");
        });
    }

    // keep your slugging logic as-is
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
