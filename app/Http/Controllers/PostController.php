<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * List posts (dashboard feed) with search (?q=)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = Post::with(['user', 'media'])
            ->when($q !== '', function ($builder) use ($q) {
                $driver = DB::getDriverName();

                if ($driver === 'mysql') {
                    // Try FULLTEXT (requires optional migration below)
                    try {
                        $builder->whereFullText(['title', 'description'], $q, ['mode' => 'boolean']);
                        return;
                    } catch (\Throwable $e) {
                        // fall through to LIKE
                    }
                }

                $op = $driver === 'pgsql' ? 'ilike' : 'like';
                $builder->where(function ($qb) use ($q, $op) {
                    $qb->where('description', $op, "%{$q}%")
                       ->orWhere('title', $op, "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('posts.index', compact('posts', 'q'));
    }

    /**
     * Store a new post (one image OR one video URL)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|string|max:5000',
            'media.*'     => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,avi,mkv|max:20480',
            'media_url'   => 'nullable|url|max:2048',
        ]);

        $hasImage = $request->hasFile('media');
        $hasUrl   = $request->filled('media_url');

        // exclusivity
        if ($hasImage && $hasUrl) {
            return back()
                ->withErrors(['media_url' => 'Choose either an image OR a video URL, not both.'])
                ->withInput();
        }
        if (!$hasImage && !$hasUrl) {
            return back()
                ->withErrors(['media' => 'Please add an image or a video URL.'])
                ->withInput();
        }
        if ($hasImage && count($request->file('media', [])) > 1) {
            return back()
                ->withErrors(['media' => 'Please upload only one image.'])
                ->withInput();
        }

        // auto title (trim spaces, strip tags, normalize whitespace)
        $rawDesc   = (string) ($data['description'] ?? '');
        $autoTitle = trim(preg_replace('/\s+/', ' ', strip_tags($rawDesc)));

        if ($autoTitle === '') {
            $autoTitle = $hasImage ? 'Image post' : 'Video post';
        }

        // hard cap title length ~80 chars
        if (Str::length($autoTitle) > 80) {
            $autoTitle = Str::limit($autoTitle, 80, '');
        }

        // create post
        $post = Post::create([
            'user_id'     => auth()->id(),
            'title'       => $autoTitle,
            'description' => $data['description'] ?? null,
        ]);

        // attach media
        if ($hasImage) {
            $file = $request->file('media')[0];
            $path = $file->store('posts/' . date('Y/m/d'), 'public');

            PostMedia::create([
                'post_id'     => $post->id,
                'file_path'   => $path,
                'youtube_url' => null,
                'media_type'  => 'image',
            ]);
        } elseif ($hasUrl) {
            $url = $request->input('media_url');

            PostMedia::create([
                'post_id'     => $post->id,
                'file_path'   => null,
                'youtube_url' => $url,
                'media_type'  => 'video',
            ]);
        }

        return back()->with('message', 'Posted successfully!');
    }

    /**
     * Update post (used by the edit modal)
     * Route Model Binding by slug: /posts/{post:slug}
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'description'   => 'nullable|string|max:5000',
            'media.*'       => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov,avi,mkv|max:20480',
            'media_url'     => 'nullable|url|max:2048',
            'remove_media'  => 'nullable|in:0,1',
        ]);

        $removeMedia = $request->input('remove_media') === '1';
        $hasImage    = $request->hasFile('media');
        $hasUrl      = $request->filled('media_url');

        if (!$removeMedia && $hasImage && $hasUrl) {
            return back()->withErrors([
                'media_url' => 'Choose either an image OR a video URL, not both (or click Remove media).'
            ])->withInput();
        }

        // update description + auto title
        $post->description = $data['description'] ?? $post->description;
        $titleSource       = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->description)));
        if ($titleSource !== '') {
            $post->title = Str::limit($titleSource, 255, '');
        }
        $post->save();

        // handle media
        if ($removeMedia) {
            $this->deleteMediaFiles($post);
        } elseif ($hasImage) {
            // replace existing with new image
            $this->deleteMediaFiles($post);

            $file = $request->file('media')[0];
            $path = $file->store('posts/' . date('Y/m/d'), 'public');

            PostMedia::create([
                'post_id'     => $post->id,
                'file_path'   => $path,
                'youtube_url' => null,
                'media_type'  => 'image',
            ]);
        } elseif ($hasUrl) {
            // replace existing with YouTube URL
            $this->deleteMediaFiles($post);

            $url = $request->input('media_url');

            PostMedia::create([
                'post_id'     => $post->id,
                'file_path'   => null,
                'youtube_url' => $url,
                'media_type'  => 'video',
            ]);
        }

        return back()->with('message', 'Post updated.');
    }

    /**
     * Destroy a post
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $this->deleteMediaFiles($post);
        $post->delete();

        return back()->with('message', 'Post deleted.');
    }

    /**
     * Public show page (used by "Share" link)
     */
    public function show(Post $post)
    {
        $post->load(['user', 'media']);
        return view('posts.show', compact('post'));
    }

    /**
     * Remove physical files for local media and delete media rows.
     * (YouTube rows have no file_path and will not attempt file deletion.)
     */
    protected function deleteMediaFiles(Post $post): void
    {
        $post->loadMissing('media');

        foreach ($post->media as $m) {
            // delete physical file only if a local file exists
            if ($m->file_path && \Storage::disk('public')->exists($m->file_path)) {
                \Storage::disk('public')->delete($m->file_path);
            }
            $m->delete();
        }
    }

    /**
     * Public index (10 per page) with search (?q=)
     */
    public function publicIndex(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = Post::with(['user','media'])
            ->when($q !== '', function ($builder) use ($q) {
                $driver = DB::getDriverName();

                if ($driver === 'mysql') {
                    try {
                        $builder->whereFullText(['title', 'description'], $q, ['mode' => 'boolean']);
                        return;
                    } catch (\Throwable $e) {
                        // fall through
                    }
                }

                $op = $driver === 'pgsql' ? 'ilike' : 'like';
                $builder->where(function ($qb) use ($q, $op) {
                    $qb->where('description', $op, "%{$q}%")
                       ->orWhere('title', $op, "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['q' => $q]);

        return view('posts.public', compact('posts', 'q'));
    }
}
