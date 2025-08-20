@extends('layouts.app')

@section('title', 'All Posts')
@section('page_title', 'Public Feeds')

{{-- Optional: no-flicker dark boot (keep if layout doesn’t already handle it) --}}
@section('head')
<script>
(function() {
    const ls = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (ls === 'dark' || (!ls && prefersDark)) document.documentElement.classList.add('dark');
})();
</script>
@endsection

@push('styles')
<style>
/* ====== Feed + Sidebar layout (8/4 feel) ====== */
.feed-wrap {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .feed-wrap {
        grid-template-columns: minmax(0, 1fr) 360px;
        /* left grows, right fixed */
        align-items: start;
    }
}

/* Sticky sidebar card (right column) — page scrolls, sidebar sticks */
.sidebar-sticky {
    position: sticky;
    top: var(--sticky-top, 84px);
}

/* Sidebar cards */
.side-card {
    border-radius: 1rem;
    background: rgba(255, 255, 255, .9);
    border: 1px solid rgba(226, 232, 240, .7);
    box-shadow: 0 1px 2px rgba(2, 6, 23, .06), 0 12px 36px -24px rgba(2, 6, 23, .45);
}

html.dark .side-card {
    background: rgba(15, 23, 42, .72);
    border-color: rgba(30, 41, 59, .7);
}

/* Stat row */
.stat {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem 1rem;
    border-radius: .75rem;
    border: 1px solid rgba(226, 232, 240, .7);
}

html.dark .stat {
    border-color: rgba(30, 41, 59, .7);
}

/* Social grid */
.social-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: .75rem;
}

@media (min-width:1280px) {
    .social-grid {
        grid-template-columns: repeat(6, 1fr);
    }
}

.social-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, .45);
    background: rgba(241, 245, 249, .7);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease;
}

html.dark .social-pill {
    background: rgba(2, 6, 23, .6);
    border-color: rgba(51, 65, 85, .6);
}

.social-pill .icon {
    width: 22px;
    height: 22px;
    opacity: .9;
    fill: #38bdf8;
}

.social-pill:hover {
    transform: translateY(-2px);
}

.social-pill.is-active {
    background:
        radial-gradient(120px 120px at 30% 20%, color-mix(in oklab, var(--brand-start, #60a5fa), white 10%), transparent),
        radial-gradient(120px 120px at 70% 80%, color-mix(in oklab, var(--brand-end, #3b82f6), white 8%), transparent);
    border-color: color-mix(in oklab, var(--brand-start, #60a5fa), #000 12%);
    box-shadow: 0 10px 26px -18px color-mix(in oklab, var(--brand-end, #3b82f6), #000 35%);
}

.social-pill.is-active .icon {
    fill: #0ea5e9;
}

.social-pill.is-empty {
    opacity: .7;
}

.social-pill.is-empty:hover {
    opacity: 1;
}

/* Tiny tooltip */
.tooltip {
    position: relative;
}

.tooltip[data-tip]:hover::after {
    content: attr(data-tip);
    position: absolute;
    bottom: 58px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    font-size: 12px;
    font-weight: 600;
    padding: 6px 8px;
    border-radius: 8px;
    background: #0f172a;
    color: #fff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(2, 6, 23, .35);
}

html.dark .tooltip[data-tip]:hover::after {
    background: #0b1220;
    color: #e5e7eb;
    border-color: #1f2937;
}
</style>
@endpush

@section('content')
@php
use Illuminate\Pagination\LengthAwarePaginator;

// Logged-in user (if any)
$me = auth()->user();

// Choose the “owner” account whose links should show publicly when guest
$owner = \App\Models\User::where('is_admin', true)->first()
?? \App\Models\User::orderBy('id')->first();

// Social links: logged-in user’s own, else owner’s
$social = ($me?->social_links) ?: ($owner?->social_links ?? []);

// Count from $posts (paginator-aware)
$publicCount = $posts instanceof LengthAwarePaginator
? $posts->total()
: (is_countable($posts) ? count($posts) : 0);

// social icons & brand colors (single source of truth)
$icons = [
'facebook' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3V2z',
'twitter' => 'M22 5.8c-.7.3-1.5.5-2.2.6.8-.5 1.4-1.2 1.7-2.1-.8.5-1.7.8-2.6 1A4 4 0 0012 8.5c0 .3 0 .6.1.9A11.3 11.3 0
013 5.1a4 4 0 001.2 5.3c-.6 0-1.1-.2-1.6-.4v.1a4 4 0 003.2 3.9c-.5.2-1 .2-1.6.1a4 4 0 003.7 2.8A8.1 8.1 0 012 18.6 11.5
11.5 0 008.3 20c7 0 10.9-5.8 10.9-10.9v-.5c.8-.5 1.5-1.2 2-2z',
'youtube' => 'M10 15l5.2-3L10 9v6z M21.6 7.2c.2.8.4 2 .4 4s-.2 3.2-.4 4c-.2.8-.8 1.4-1.6 1.6C18.6 17 12 17 12 17s-6.6
0-8-.2a2.2 2.2 0 01-1.6-1.6C2.2 14.4 2 13.2 2 11.2s.2-3.2.4-4C2.6 6.4 3.2 5.8 4 5.6 5.4 5.4 12 5.4 12 5.4s6.6 0 8
.2c.8.2 1.4.8 1.6 1.6z',
'wordpress' => 'M4 12a8 8 0 1016 0A8 8 0 004 12zm13.7-1.1a6.7 6.7 0 01-2.1 6.2l-2.3-6.2 1.1-3 3.3 3zM8.7 7.9c.5 0 .8.2 1
.6l2.4 6.5-1.3 3.3A6.7 6.7 0 017.1 8c.4-.1 1-.1 1.6-.1zm6 .1c.5 0 1 .1 1.3.2.4.8.6 1.6.6 2.6 0 1-.4 2.1-1 3.4l-1.8-5
.9-1.2z',
'instagram' => 'M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0 000-10zm6-1a1 1
0 110 2 1 1 0 010-2z',
'quora' => 'M12 2a8 8 0 100 16 7.9 7.9 0 005.8-2.5l1.9 2.2 1.4-1.2-2-2.3A7.8 7.8 0 0020 10c0-4.4-3.6-8-8-8zm0 3a5 5 0
015 5c0 2.6-2 4.7-4.5 5l1.3 1.6h-2.1l-1.1-1.4A5 5 0 017 10a5 5 0 015-5z',
'pinterest' => 'M12 2a10 10 0 00-3.6 19.3c-.1-.8-.2-2 .1-2.8l2-8s-.5-1 0-1.6c.6-.7 1.7-.5 2.3 0 .7.6.6 1.7.4 2.6l-1.3
5a3.5 3.5 0 005.9-2.8c0-3.2-2.3-5.4-5.6-5.4-3.8 0-6.1 2.8-6.1 5.8 0 1.4.5 2.9 1.6 3.7.2.2.3.1.4-.1l.4-1.6c0-.2
0-.3-.1-.5a3.3 3.3 0 01-.1-2.3c.4-1.2 1.7-2 3-2 2 0 3.3 1.4 3.3 3.3 0 2.3-1.1 3.9-2.8 3.9-.9 0-1.6-.7-1.4-1.6l.4-1.5',
'linkedin' => 'M4 3a2 2 0 012-2h12a2 2 0 012 2v18a2 2 0 01-2 2H6a2 2 0 01-2-2V3zm3 6h3v10H7V9zm1.5-5a1.7 1.7 0 100 3.4
1.7 1.7 0 000-3.4zM12 9h3v1.5c.6-.9 1.6-1.7 3-1.7 3 0 3.5 2 3.5 4.6V19h-3v-4.3c0-1.1 0-2.4-1.5-2.4s-1.7 1.2-1.7
2.3V19H12V9z',
'blogger' => 'M6 3h8a5 5 0 015 5v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8a5 5 0 015-5zm2 4a2 2 0 100 4h4a2 2 0 100-4H8zm0 6a2 2 0
100 4h6a2 2 0 100-4H8z',
'website' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0
12-2.7-2.2-2.7-9.8 0-12zM6 12h12M6 14h12',
];
$brand = [
'facebook' => ['#061c38ff','#0E5AD1'],
'twitter' => ['#061824ff','#0C8BD6'],
'youtube' => ['#3a0101ff','#D40000'],
'wordpress'=> ['#012738ff','#125E7F'],
'instagram'=> ['#3b1c03ff','#DD2A7B'],
'quora' => ['#4a0805ff','#8C1E1B'],
'pinterest'=> ['#2f0209ff','#B8001C'],
'linkedin' => ['#021f3dff','#004182'],
'blogger' => ['#3e1b05ff','#E26314'],
'website' => ['#02333bff','#3B82F6'],
];
@endphp

<div class="max-w-6xl mx-auto">
    <div class="feed-wrap">

        {{-- ================= LEFT: FEED ================= --}}
        <div class="space-y-6">
            @if(!empty($q))
            <div class="text-sm text-slate-500">
                Showing results for: <span class="font-semibold">“{{ $q }}”</span>
                <a href="{{ url()->current() }}" class="ml-2 text-blue-600 hover:underline">Clear</a>
            </div>
            @endif

            @forelse($posts as $post)
            <article class="soft-card soft-card-hover spotlight p-5">
                <header class="flex items-start gap-3">
                    @php $author = $post->user; @endphp
                    @if($author && $author->avatar_path)
                    <img src="{{ asset('storage/'.$author->avatar_path) }}?v={{ optional($author->updated_at)->timestamp }}"
                        alt="{{ $author->username ?? $author->email }} avatar"
                        class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/60 dark:ring-slate-900/60"
                        loading="lazy" decoding="async">
                    @else
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60 flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr($author->username ?? $author->email ?? 'U', 0, 1)) }}
                    </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">
                                    {{ $post->user->username ?? $post->user->email ?? 'User' }}
                                </a>
                            </h3>
                            <span class="text-xs text-slate-400">•</span>
                            <span
                                class="text-xs text-slate-500 dark:text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>
                        </div>

                        @if($post->description)
                        <div class="mt-2 text-slate-400 dark:text-slate-500 leading-7">
                            <div class="post-desc break-words" data-full='@json($post->description)'></div>
                            <button type="button"
                                class="readmore-btn text-sm font-semibold text-blue-600 hover:underline mt-1 hidden">Read
                                more</button>
                        </div>
                        @endif
                    </div>
                </header>

                {{-- Media --}}
                @if($post->media->isNotEmpty())
                @php $m = $post->media->first(); @endphp
                <div class="mt-4">
                    @if($m->media_type === 'image' && $m->file_path)
                    <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank" class="block media-frame">
                        <img src="{{ asset('storage/'.$m->file_path) }}" alt="Post image"
                            class="w-full max-h-[560px] object-cover" loading="lazy">
                    </a>

                    @elseif($m->media_type === 'video')
                    @php
                    $ytUrl = $m->youtube_url ?? null; $ytId = null;
                    if ($ytUrl && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w\-]{6,})~i',
                    $ytUrl, $mm)) $ytId = $mm[1];
                    $embed = $ytId ? "https://www.youtube-nocookie.com/embed/{$ytId}?rel=0&modestbranding=1" : null;
                    $thumb = $ytId ? "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg" : null;
                    @endphp

                    @if($embed)
                    <div class="media-frame aspect-video relative">
                        <iframe src="{{ $embed }}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        <a href="{{ $ytUrl }}" target="_blank" rel="noopener"
                            class="absolute right-2 bottom-2 bg-white/90 text-black text-xs px-2.5 py-1 rounded-lg shadow hover:bg-white">
                            Watch on YouTube ↗
                        </a>
                    </div>
                    @elseif($m->file_path)
                    <div class="media-frame">
                        <video controls playsinline src="{{ asset('storage/'.$m->file_path) }}"
                            class="w-full max-h-[560px] object-cover"></video>
                    </div>
                    @elseif($thumb && $ytUrl)
                    <a href="{{ $ytUrl }}" target="_blank" rel="noopener" class="block media-frame">
                        <img src="{{ $thumb }}" alt="Watch on YouTube" class="w-full object-cover">
                    </a>
                    @endif

                    @elseif($m->media_type === 'url' && $m->file_path)
                    <div class="p-4">
                        <a href="{{ $m->file_path }}" target="_blank"
                            class="text-indigo-600 dark:text-indigo-300 hover:underline">{{ $m->file_path }}</a>
                    </div>
                    @endif
                </div>
                @endif

                <footer
                    class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between gap-3">
                    <a href="{{ route('posts.show', $post->slug) }}" class="btn-ghost text-sm">View</a>
                    <button type="button" class="btn-ghost text-sm share-btn"
                        data-url="{{ route('posts.show', $post->slug) }}">Share</button>
                </footer>
            </article>
            @empty
            <div class="soft-card p-6 text-center text-slate-500">
                @if(!empty($q)) No posts matched “{{ $q }}”. @else No posts yet. @endif
            </div>
            @endforelse

            {{-- Pagination --}}
            @if ($posts->hasPages())
            <div class="flex items-center justify-between py-4">
                <div class="text-slate-500 dark:text-slate-400 text-sm">
                    Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}
                </div>
                <div class="flex gap-2">
                    @if ($posts->onFirstPage())
                    <span class="btn-ghost opacity-60 text-sm">‹ Prev</span>
                    @else
                    <a href="{{ $posts->previousPageUrl() }}" class="btn-ghost text-sm">‹ Prev</a>
                    @endif

                    @if ($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="btn-ghost text-sm">Next ›</a>
                    @else
                    <span class="btn-ghost opacity-60 text-sm">Next ›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ================= RIGHT: STICKY SIDEBAR ================= --}}
        <aside class="sidebar-sticky space-y-4">

            {{-- Profile summary: ONLY for logged-in users --}}
            @auth
            <div class="side-card p-5">
                <div class="flex items-center gap-3">
                    @if($me?->avatar_path)
                    <img src="{{ asset('storage/'.$me->avatar_path) }}?v={{ optional($me->updated_at)->timestamp }}"
                        class="w-12 h-12 rounded-xl object-cover ring-2 ring-white/60 dark:ring-slate-900/60"
                        alt="Avatar">
                    @else
                    <div
                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($me->username ?? $me->email ?? 'U', 0, 1)) }}
                    </div>
                    @endif

                    <div class="min-w-0">
                        <div class="font-extrabold truncate">{{ $me->name ?? $me->username }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ '@'.$me->username }}</div>
                    </div>
                </div>

                <div class="mt-4 stat">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Total posts</span>
                    <span class="text-lg font-extrabold">{{ number_format($publicCount ?? 0) }}</span>
                </div>

                <a href="{{ Route::has('profile.show') ? route('profile.show') : url('/profile') }}"
                    class="mt-3 btn w-full justify-center btn-primary">Go to profile</a>
            </div>
            @endauth

            {{-- Social: ALWAYS visible (guests see the owner’s links) --}}
            <div class="side-card p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold">Social</h3>
                    @auth
                    <a href="{{ Route::has('profile.show') ? route('profile.show') : url('/profile') }}"
                        class="text-sm text-blue-600 hover:underline">Edit links</a>
                    @endauth
                </div>

                <div class="mt-3 social-grid">
                    @php
                    // Map your keys -> Simple Icons slugs (keeps both twitter & x)
                    $slugMap = [
                    'facebook'=>'facebook', 'twitter'=>'x', 'youtube'=>'youtube',
                    'wordpress'=>'wordpress', 'instagram'=>'instagram', 'quora'=>'quora',
                    'pinterest'=>'pinterest', 'linkedin'=>'linkedin', 'blogger'=>'blogger', 'website'=>null,
                    ];
                    @endphp

                    @foreach($icons as $key => $unusedPath) {{-- $unusedPath from old array is ignored --}}
                    @php
                    $slug = $slugMap[$key] ?? null;
                    // try exact key; if "x" has no URL, fall back to legacy "twitter"
                    $url = $social[$key] ?? ($key === 'x' ? ($social['twitter'] ?? null) : null);
                    [$c1,$c2] = $brand[$key] ?? ['#0f172a','#334155'];
                    $tipRaw = ($key === 'x' || $key === 'twitter') ? 'X (Twitter)' : $key;
                    $tip = ucfirst($tipRaw);

                    // Use white glyph for ACTIVE; slate-400 for EMPTY (better in light mode)
                    $emptyHex = '94a3b8';
                    $imgSrc = $slug ? "https://cdn.simpleicons.org/{$slug}/" . ($url ? 'ffffff' : $emptyHex) : null;

                    // LinkedIn: inline SVG to dodge ad-blockers + follow currentColor
                    $linkedinPath = null;
                    if ($slug === 'linkedin') {
                    $imgSrc = null;
                    $linkedinPath = 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136
                    1.447-2.136 2.944v5.662H9.352V9h3.414v1.561h.049c.476-.9 1.637-1.852 3.368-1.852 3.602 0 4.268 2.371
                    4.268 5.455v6.288zM5.337 7.433a2.062 2.062 0 11.001-4.124 2.062 2.062 0 01-.001 4.124zM6.96
                    20.452H3.713V9H6.96v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.728v20.543C0 23.226.792 24 1.771
                    24h20.451C23.2 24 24 23.226 24 22.271V1.728C24 .774 23.2 0 22.222 0h.003z';
                    }
                    @endphp

                    @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="tooltip social-pill is-active"
                        style="--brand-start: {{ $c1 }}; --brand-end: {{ $c2 }};" data-tip="{{ $tip }}"
                        aria-label="{{ $tip }}">
                        @if($linkedinPath)
                        <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="{{ $linkedinPath }}" />
                        </svg>
                        @elseif($imgSrc)
                        <img src="{{ $imgSrc }}" alt="" class="icon" loading="lazy" decoding="async" />
                        @else
                        {{-- Generic globe for "website" --}}
                        <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor"
                                d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0 12-2.7-2.2-2.7-9.8 0-12zM6 12h12M6 14h12" />
                        </svg>
                        @endif
                        <span class="sr-only">{{ $tip }}</span>
                    </a>
                    @else
                    <span class="tooltip social-pill is-empty" data-tip="{{ $tip }}" aria-label="{{ $tip }}">
                        @if($linkedinPath)
                        <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="{{ $linkedinPath }}" />
                        </svg>
                        @elseif($imgSrc)
                        <img src="{{ $imgSrc }}" alt="" class="icon" loading="lazy" decoding="async" />
                        @else
                        <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor"
                                d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0 12-2.7-2.2-2.7-9.8 0-12zM6 12h12M6 14h12" />
                        </svg>
                        @endif
                    </span>
                    @endif
                    @endforeach
                </div>
            </div>
            {{-- Powered by Postify --}}
            <div class="side-card brand-card p-5">
                <div class="flex items-center gap-4">
                    <div class="brand-logo" aria-hidden="true">
                        {{-- simple “posts grid” logo --}}
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <div class="brand-chip mb-1.5">
                            <span>Powered by</span><span>Postify</span>
                        </div>
                        <div class="font-extrabold text-lg leading-tight">Cotocus Company</div>
                        <div class="brand-meta mt-1">© {{ date('Y') }} · All rights reserved</div>
                    </div>
                </div>
            </div>

        </aside>
    </div>
</div>
<footer class="mt-6">
    <div class="w-full px-3">
        <div class="flex items-center justify-center">
            <div class="inline-flex items-center gap-2 rounded-2xl px-3.5 py-2 shadow-sm
               border bg-white/90 text-slate-700 border-slate-200
               dark:bg-slate-900/80 dark:text-slate-300 dark:border-slate-700 transition-colors">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 14.93V14h-2v2.93A8 8 0 014.07 13H6v-2H4.07A8 8 0 0111 7.07V10h2V7.07A8 8 0 0119.93 11H18v2h1.93A8 8 0 0113 16.93Z" />
                </svg>
                <span class="text-xs font-semibold">Powered by</span>
                <span class="text-xs font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Postify</span>
                <span class="mx-1.5 text-slate-300 dark:text-slate-600">•</span>
                <span class="text-xs">© {{ date('Y') }} Cotocus Company — All rights reserved</span>
            </div>
        </div>
    </div>
</footer>

{{-- Center toast (same look as index) --}}
<div id="toast" class="toast">
    <span class="inline-block w-2 h-2 rounded-full bg-blue-400 shadow-[0_0_0_4px_rgba(59,130,246,.18)]"></span>
    <span id="toastText" class="text-sm font-semibold">Link copied</span>
</div>

{{-- Page scripts --}}
<script>
// Center toast helper
function showToast(msg = 'Done') {
    const t = document.getElementById('toast');
    const tx = document.getElementById('toastText');
    if (!t || !tx) return;
    const header = document.querySelector('header.glass');
    const top = (header?.offsetHeight || 64) + 20;
    t.style.setProperty('--toast-top', `${top}px`);
    tx.textContent = msg;
    t.classList.add('is-visible');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove('is-visible'), 1600);
}

// Share buttons
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const url = btn.dataset.url;
        try {
            if (navigator.share && window.isSecureContext) {
                await navigator.share({
                    title: document.title,
                    url
                });
                return;
            }
        } catch (_) {}
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(url);
                showToast('Link copied');
                return;
            }
        } catch (_) {}
        const ta = document.createElement('textarea');
        ta.value = url;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
        showToast('Link copied');
    });
});

// Description clamp/linkify
(function enhanceDescriptions() {
    const MAX_WORDS = 250,
        LINE_CLAMP = 4;
    const blocks = document.querySelectorAll('.post-desc');
    if (!blocks.length) return;

    const escapeHTML = s => String(s)
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    const linkify = t =>
        t.replace(/\b((https?:\/\/|www\.)[^\s<]+[^\s<\.)])/gi, m =>
            `<a href="${m.startsWith('http')?m:'https://'+m}" target="_blank" rel="noopener noreferrer" class="underline break-words">${m}</a>`
        );
    const nl2br = s => s.replace(/\n/g, "<br>");
    const applyClamp = (el, on) => {
        if (on) {
            el.style.display = '-webkit-box';
            el.style.webkitBoxOrient = 'vertical';
            el.style.webkitLineClamp = String(LINE_CLAMP);
            el.style.overflow = 'hidden';
        } else {
            el.style.display = '';
            el.style.webkitBoxOrient = '';
            el.style.webkitLineClamp = '';
            el.style.overflow = '';
        }
    };
    const truncateWords = (s, max) => {
        const w = s.trim().split(/\s+/);
        return w.length <= max ? [s, false] : [w.slice(0, max).join(' ') + '…', true];
    };
    const decodeData = el => {
        const raw = el.dataset.full ?? '';
        try {
            if (raw[0] === '"' || /\\u[0-9a-fA-F]{4}/.test(raw)) return JSON.parse(raw);
        } catch (_) {}
        return raw;
    };
    const render = txt => nl2br(linkify(escapeHTML(txt).replace(/\n{2,}/g, '\n')));

    blocks.forEach(el => {
        const btn = el.parentElement.querySelector('.readmore-btn');
        const full = decodeData(el);
        const [trunc, wasTrim] = truncateWords(full, MAX_WORDS);

        el.innerHTML = render(trunc);
        applyClamp(el, true);

        let needs = wasTrim;
        requestAnimationFrame(() => {
            const tmp = document.createElement('div');
            tmp.className = el.className;
            tmp.style.position = 'absolute';
            tmp.style.visibility = 'hidden';
            tmp.style.width = el.clientWidth + 'px';
            tmp.innerHTML = render(full);
            document.body.appendChild(tmp);
            const fullH = tmp.scrollHeight;
            tmp.remove();

            const clampH = el.getBoundingClientRect().height;
            if (fullH > clampH) needs = true;

            if (needs && btn) {
                btn.classList.remove('hidden');
                let open = false;
                btn.addEventListener('click', () => {
                    open = !open;
                    if (open) {
                        el.innerHTML = render(full);
                        applyClamp(el, false);
                        btn.textContent = 'Read less';
                    } else {
                        el.innerHTML = render(trunc);
                        applyClamp(el, true);
                        btn.textContent = 'Read more';
                    }
                });
            }
        });
    });
})();
</script>
@endsection