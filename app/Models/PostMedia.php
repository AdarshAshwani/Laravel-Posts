<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'post_media';

    // Add youtube_url to fillable
    protected $fillable = [
        'post_id',
        'file_path',    // local image/video stored on "public" disk
        'youtube_url',  // full YouTube link (optional)
        'media_type',   // 'image' or 'video'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Returns a URL that can be opened directly:
     * - For local uploads => storage URL
     * - For YouTube => the original youtube_url
     */
    public function url(): string
    {
        if ($this->isYoutube() && $this->youtube_url) {
            return $this->youtube_url;
        }

        return $this->file_path
            ? asset('storage/'.$this->file_path)
            : '';
    }

    /** True if this media is a YouTube link (stored as "video" with youtube_url set) */
    public function isYoutube(): bool
    {
        return $this->media_type === 'video' && !empty($this->youtube_url);
    }

    /** Extract the YouTube video ID from youtube_url (if present) */
    public function youtubeId(): ?string
    {
        if (!$this->isYoutube()) return null;

        $url = $this->youtube_url;
        if (preg_match('/(?:youtube\.com.*[?&]v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
            return $m[1];
        }
        return null;
    }

    /** Return an embeddable URL for YouTube (e.g. https://www.youtube.com/embed/VIDEO_ID) */
    public function embedUrl(): ?string
    {
        $id = $this->youtubeId();
        return $id ? "https://www.youtube.com/embed/{$id}" : null;
    }

    /** Optional: thumbnail URL for YouTube */
    public function youtubeThumbnail(): ?string
    {
        $id = $this->youtubeId();
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : null;
    }
}
