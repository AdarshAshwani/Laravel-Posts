@extends('layouts.app')

@section('title','Posts')


@section('content')
@php
$me = auth()->user();
$social = $me?->social_links ?? [];

// total posts
$myPostCount = $posts instanceof \Illuminate\Pagination\LengthAwarePaginator
? $posts->total()
: (is_countable($posts) ? count($posts) : 0);

// Simple Icons slugs (keep both twitter & x)
$icons = [
'facebook' => 'facebook',
'x' => 'x', // new key also supported
'youtube' => 'youtube',
'wordpress' => 'wordpress',
'instagram' => 'instagram',
'quora' => 'quora',
'pinterest' => 'pinterest',
'linkedin' => 'linkedin',
'blogger' => 'blogger',
'website' => null, // generic globe
];

// gradients
$brand = [
'facebook' => ['#031e41ff','#0E5AD1'],
'x' => ['#072132ff','#0C8BD6'],
'youtube' => ['#830303ff','#D40000'],
'wordpress' => ['#092634ff','#125E7F'],
'instagram' => ['#F58529','#DD2A7B'],
'quora' => ['#B92B27','#8C1E1B'],
'pinterest' => ['#E60023','#B8001C'],
'linkedin' => ['#0A66C2','#004182'],
'blogger' => ['#FF8030','#E26314'],
'website' => ['#07063aff','#3B82F6'],
];
@endphp

<div class="max-w-6xl mx-auto">
    <div class="feed-wrap">

        {{-- ================= LEFT: FEED (scrolls) ================= --}}
        <div class="feed-scroller space-y-6">
            @if(!empty($q))
            <div class="text-sm text-slate-500 mb-3">
                Showing results for: <span class="font-semibold">“{{ $q }}”</span>
                <a href="{{ url()->current() }}" class="ml-2 text-blue-600 hover:underline">Clear</a>
            </div>
            @endif

            {{-- Composer --}}
            <button id="openCreate" class="composer-launch">
                @if(auth()->check() && auth()->user()->avatar_path)
                <img src="{{ asset('storage/'.auth()->user()->avatar_path) }}?v={{ optional(auth()->user()->updated_at)->timestamp }}"
                    alt="{{ auth()->user()->username }} avatar"
                    class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/60 dark:ring-slate-900/60" loading="lazy"
                    decoding="async">
                @else
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->username ?? auth()->user()->email ?? 'U', 0, 1)) }}
                </div>
                @endif
                <div class="flex-1 text-slate-500 dark:text-slate-400">Share your thoughts, links, or updates…</div>
                <div class="btn btn-primary text-sm">Create</div>
            </button>

            {{-- Feed --}}
            @forelse($posts as $post)
            @php $m = $post->media->first(); $author = $post->user; @endphp
            <article class="soft-card soft-card-hover spotlight p-5">
                <header class="flex items-start gap-3">
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
                                <a href="{{ Route::has('profile.show') ? route('profile.show') : url('/profile') }}"
                                    class="hover:underline">
                                    {{ $post->user->username ?? $post->user->email }}
                                </a>
                            </h3>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        @if($post->description)
                        <div class="mt-1 text-slate-500 dark:text-slate-200 leading-7">
                            <div class="post-desc break-words" data-full='@json($post->description)' data-json="1">
                            </div>
                            <button type="button"
                                class="readmore-btn text-sm font-semibold text-blue-600 hover:underline mt-1 hidden">Read
                                more</button>
                        </div>
                        @endif
                    </div>
                </header>

                {{-- first media only --}}
                @if($post->media->isNotEmpty())
                <div class="mt-4">
                    @if($m->media_type === 'image' && $m->file_path)
                    <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank" class="block media-frame">
                        <img src="{{ asset('storage/'.$m->file_path) }}" class="w-full max-h-[520px] object-cover"
                            loading="lazy" alt="">
                    </a>
                    @elseif($m->media_type === 'video')
                    @php
                    $yt = $m->youtube_url ?? null; $embed = null;
                    if ($yt && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w\-]{6,})~i',
                    $yt, $mm)) $embed = 'https://www.youtube.com/embed/'.$mm[1];
                    @endphp
                    @if($embed)
                    <div class="media-frame aspect-video">
                        <iframe src="{{ $embed }}" class="w-full h-full" allowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
                    </div>
                    @elseif($m->file_path)
                    <div class="media-frame">
                        <video class="w-full max-h-[520px] object-cover" src="{{ asset('storage/'.$m->file_path) }}"
                            controls playsinline></video>
                    </div>
                    @endif
                    @endif
                </div>
                @endif

                {{-- Actions --}}
                <footer class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-800/60">
                    <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-300">
                        <button type="button" class="flex items-center gap-1 hover:text-blue-600 open-edit"
                            data-slug="{{ $post->slug }}" data-description="{{ e($post->description) }}"
                            data-media-type="{{ optional($m)->media_type }}"
                            data-media-path="{{ optional($m)->file_path }}"
                            data-media-url="{{ optional($m)->youtube_url }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.83z" />
                            </svg>
                            Edit
                        </button>

                        <form method="POST" action="{{ route('posts.destroy', $post->slug) }}"
                            onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button class="flex items-center gap-1 hover:text-rose-600">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                </svg>
                                Delete
                            </button>
                        </form>

                        <button type="button" class="flex items-center gap-1 hover:text-emerald-600 share-btn"
                            data-url="{{ route('posts.show', $post->slug) }}">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M14 9l-1-1-4 4 4 4 1-1-3-3 3-3zm2-8H8C6.9 1 6 1.9 6 3v4h2V4h8v16H8v-3H6v4c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2z" />
                            </svg>
                            Share
                        </button>
                    </div>
                </footer>
            </article>
            @empty
            <div class="soft-card p-6 text-center text-slate-500">
                @if(!empty($q)) No posts matched “{{ $q }}”. @else No posts yet. @endif
            </div>
            @endforelse

            <div class="mt-6">{{ $posts->links() }}</div>
        </div>

        {{-- ================= RIGHT: STICKY SIDEBAR ================= --}}
        <aside class="sidebar-sticky space-y-4">
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
                    <span class="text-lg font-extrabold">{{ number_format($myPostCount) }}</span>
                </div>

                <a href="{{ Route::has('profile.show') ? route('profile.show') : url('/profile') }}"
                    class="mt-3 btn w-full justify-center btn-primary">Go to profile</a>
            </div>

            <div class="side-card p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold">Social</h3>
                    <a href="{{ Route::has('profile.show') ? route('profile.show') : url('/profile') }}"
                        class="text-sm text-blue-600 hover:underline">Edit links</a>
                </div>

                <div class="mt-3 social-grid">
                    @foreach($icons as $key => $slug)
                    @php
                    // try the exact key; if "x" has no URL, try legacy "twitter"
                    $url = $social[$key] ?? ($key === 'x' ? ($social['twitter'] ?? null) : null);

                    [$c1, $c2] = $brand[$key] ?? ['#0f172a','#334155'];
                    $tipRaw = $key === 'x' || $key === 'twitter' ? 'X (Twitter)' : $key;
                    $tip = ucfirst($tipRaw);

                    // Use white for ACTIVE, slate-400 for EMPTY
                    $emptyHex = '94a3b8'; // slate-400
                    $imgSrc = $slug
                    ? "https://cdn.simpleicons.org/{$slug}/" . ($url ? 'ffffff' : $emptyHex)
                    : null;

                    // LinkedIn: inline to avoid ad-blockers + allow CSS color via currentColor
                    $linkedinPath = null;
                    if ($slug === 'linkedin') {
                    $imgSrc = null; // we’ll render inline SVG
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
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="{{ $linkedinPath }}" />
                        </svg>
                        @elseif($imgSrc)
                        <img src="{{ $imgSrc }}" alt="" class="icon" loading="lazy" decoding="async"
                            referrerpolicy="no-referrer" />
                        @else
                        {{-- Generic globe for "website" --}}
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                            <path
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
                        {{-- Generic globe --}}
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

{{-- CREATE / EDIT MODAL --}}
<div id="postModal" class="fixed inset-0 z-50 hidden flex items-start justify-center overflow-y-auto p-4">
    <div class="fixed inset-0 bg-black/50"></div>

    <div
        class="relative w-full max-w-2xl soft-card surface-strong rounded-2xl shadow-xl flex flex-col max-h-[90vh] overflow-hidden modal-panel">
        <div
            class="flex items-center justify-between px-5 py-3 border-b border-slate-200/60 dark:border-slate-800/60 sticky top-0 glass modal-header">
            <h3 id="modalTitle" class="text-lg font-bold">Create Post</h3>
            <button id="pmClose" class="p-2 rounded hover:bg-slate-100 dark:hover:bg-slate-700">✕</button>
        </div>

        <form id="pmForm" method="POST" enctype="multipart/form-data"
            class="flex-1 overflow-y-auto px-5 py-4 space-y-5">
            @csrf
            <input type="hidden" name="title" id="autoTitle" value="">
            <input type="hidden" name="remove_media" id="pmRemoveMedia" value="0">

            <div>
                <label class="block text-sm font-semibold mb-2">Description</label>
                <textarea name="description" id="pmDescription" class="input min-h-[200px] md:min-h-[260px]"></textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="button" id="pmImageMode" class="btn-ghost">📷 Image</button>
                <button type="button" id="pmVideoMode" class="btn-ghost">🎥 Video URL</button>
                <button type="button" id="pmRemoveBtn" class="ml-auto btn-ghost text-rose-600 border-rose-300">Remove
                    media</button>
            </div>

            <div id="pmExisting" class="hidden mt-2"></div>

            <div id="pmUrlWrap" class="hidden">
                <label class="block text-sm font-semibold mb-1">Video URL</label>
                <input name="media_url" id="pmMediaUrl" type="url" placeholder="Paste a YouTube URL…" class="input" />
            </div>

            <div id="pmImageWrap" class="hidden">
                <label class="block text-sm font-semibold mb-1">Upload Image</label>
                <div id="pmDrop"
                    class="uploader rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-900/40 cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition">
                    <input id="pmFile" type="file" name="media[]" accept="image/*" class="hidden">
                    <div class="flex items-center justify-center gap-2">📁 <span>Click or drop one image</span></div>
                </div>
                <div id="pmPreview" class="mt-3"></div>
            </div>

            <div
                class="pt-4 mt-2 border-t border-slate-200/60 dark:border-slate-800/60 sticky bottom-0 glass modal-footer">
                <div class="flex items-center justify-end gap-2">
                    <button type="button" id="pmCancel" class="btn-ghost">Cancel</button>
                    <button id="pmSubmit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="toast">
    <span class="inline-block w-2 h-2 rounded-full bg-blue-400 shadow-[0_0_0_4px_rgba(59,130,246,.18)]"></span>
    <span id="toastText" class="text-sm font-semibold">Link copied</span>
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

{{-- Inline scripts --}}
<script>
// ===== Elements
const postModal = document.getElementById('postModal');
const pmForm = document.getElementById('pmForm');
const pmClose = document.getElementById('pmClose');
const pmCancel = document.getElementById('pmCancel');
const modalTitle = document.getElementById('modalTitle');
const pmSubmitBtn = document.getElementById('pmSubmit');

const pmDesc = document.getElementById('pmDescription');
const pmImageWrap = document.getElementById('pmImageWrap');
const pmUrlWrap = document.getElementById('pmUrlWrap');
const pmMediaUrl = document.getElementById('pmMediaUrl');
const pmDrop = document.getElementById('pmDrop');
const pmFile = document.getElementById('pmFile');
const pmPreview = document.getElementById('pmPreview');
const pmExisting = document.getElementById('pmExisting');
const pmRemoveBtn = document.getElementById('pmRemoveBtn');
const pmRemoveMedia = document.getElementById('pmRemoveMedia');
const pmImgMode = document.getElementById('pmImageMode');
const pmVidMode = document.getElementById('pmVideoMode');

const openCreate = document.getElementById('openCreate');

// ===== State for edit/create
let isEditing = false;
let hadExistingMedia = false; // for edit: did the post originally have media?

// ===== Toast (centered, uses your .toast/.is-visible CSS)
function showToast(msg = 'Done') {
    const toast = document.getElementById('toast');
    const text = document.getElementById('toastText');
    if (!toast || !text) return;

    // Move toast to <body> so transforms on cards can’t affect it
    if (toast.parentElement !== document.body) {
        document.body.appendChild(toast);
    }

    // Place it just below the sticky topbar if present
    const header = document.querySelector('header.glass');
    const top = (header?.offsetHeight || 64) + 20; // header height + spacing
    toast.style.setProperty('--toast-top', `${top}px`);

    text.textContent = msg;
    toast.classList.add('is-visible');

    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.classList.remove('is-visible');
    }, 1600);
}

// ===== Helpers
const ytRe = /(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([\w\-]{6,})/i;
const hasFile = () => pmFile.files && pmFile.files.length > 0;
const hasUrl = () => pmMediaUrl.value.trim().length > 0;
const validYouTube = () => !hasUrl() || ytRe.test(pmMediaUrl.value.trim()); // only validate if provided

function clearInvalid() {
    pmMediaUrl.classList.remove('is-invalid');
    pmDrop.classList.remove('is-invalid');
}

function markInvalid(which = 'auto') {
    // Prefer highlighting the active input area
    if (which === 'url' || (!hasFile() && pmUrlWrap && !pmUrlWrap.classList.contains('hidden'))) {
        pmMediaUrl.classList.add('is-invalid');
    } else {
        pmDrop.classList.add('is-invalid');
    }
}

// ===== Modal helpers
function openModal() {
    postModal.classList.remove('hidden');
}

function closeModal() {
    postModal.classList.add('hidden');
    pmForm.removeAttribute('action');
    const methodField = pmForm.querySelector('input[name="_method"]');
    if (methodField) methodField.remove();

    pmDesc.value = '';
    pmMediaUrl.value = '';
    pmFile.value = '';
    pmPreview.innerHTML = '';
    pmExisting.innerHTML = '';
    pmExisting.classList.add('hidden');

    pmRemoveMedia.value = '0';
    pmImageWrap.classList.add('hidden');
    pmUrlWrap.classList.add('hidden');
    pmImgMode.classList.remove('ring-brand');
    pmVidMode.classList.remove('ring-brand');

    clearInvalid();
    isEditing = false;
    hadExistingMedia = false;
}
pmClose?.addEventListener('click', closeModal);
pmCancel?.addEventListener('click', closeModal);
postModal?.addEventListener('click', (e) => {
    if (e.target === postModal) closeModal();
});

function setMode(mode) {
    clearInvalid();
    if (mode === 'image') {
        pmImageWrap.classList.remove('hidden');
        pmUrlWrap.classList.add('hidden');
        pmMediaUrl.value = '';
        pmImgMode.classList.add('ring-brand');
        pmVidMode.classList.remove('ring-brand');
    } else if (mode === 'video') {
        pmUrlWrap.classList.remove('hidden');
        pmImageWrap.classList.add('hidden');
        pmFile.value = '';
        pmPreview.innerHTML = '';
        pmVidMode.classList.add('ring-brand');
        pmImgMode.classList.remove('ring-brand');
    }
}
pmImgMode?.addEventListener('click', () => setMode('image'));
pmVidMode?.addEventListener('click', () => setMode('video'));

pmRemoveBtn?.addEventListener('click', () => {
    pmRemoveMedia.value = '1';
    pmMediaUrl.value = '';
    pmFile.value = '';
    pmPreview.innerHTML = '';
    pmExisting.innerHTML = '<div class="text-sm text-rose-600">Media will be removed.</div>';
    pmExisting.classList.remove('hidden');
    pmImageWrap.classList.add('hidden');
    pmUrlWrap.classList.add('hidden');
    pmImgMode.classList.remove('ring-brand');
    pmVidMode.classList.remove('ring-brand');
    clearInvalid();
});

// ===== Single image preview
pmDrop?.addEventListener('click', () => pmFile.click());
pmDrop?.addEventListener('dragover', e => {
    e.preventDefault();
    pmDrop.classList.add('uploader-active');
});
pmDrop?.addEventListener('dragleave', () => pmDrop.classList.remove('uploader-active'));
pmDrop?.addEventListener('drop', e => {
    e.preventDefault();
    pmDrop.classList.remove('uploader-active');
    if (e.dataTransfer.files?.length) setSingle(e.dataTransfer.files[0]);
});
pmFile?.addEventListener('change', e => {
    if (e.target.files?.length) {
        clearInvalid();
        setSingle(e.target.files[0]);
    }
});

function setSingle(file) {
    if (!file.type?.startsWith?.('image/')) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    pmFile.files = dt.files;
    pmPreview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-40 object-cover rounded-xl mt-2">`;
    pmRemoveMedia.value = '0';
}

// url input live validation
pmMediaUrl?.addEventListener('input', () => {
    if (pmMediaUrl.value.trim().length) {
        // user is providing URL; ensure remove flag off
        pmRemoveMedia.value = '0';
    }
    if (validYouTube()) pmMediaUrl.classList.remove('is-invalid');
});

// ===== Title from description
const autoTitleHidden = document.querySelector('input[name="title"]');

function deriveTitle() {
    const raw = (pmDesc.value || '').replace(/\s+/g, ' ').trim();
    const title = raw.length ? raw.slice(0, 70) + (raw.length > 70 ? '…' : '') : 'Post';
    autoTitleHidden.value = title;
}
pmDesc?.addEventListener('input', deriveTitle);

// ===== Create
openCreate?.addEventListener('click', () => {
    isEditing = false;
    hadExistingMedia = false;
    modalTitle.textContent = 'Create Post';
    pmSubmitBtn.textContent = 'Post';
    pmForm.setAttribute('action', "{{ route('posts.store') }}");
    const methodField = pmForm.querySelector('input[name="_method"]');
    if (methodField) methodField.remove();
    // default to image mode for creation
    setMode('image');
    openModal();
    deriveTitle();
});

// ===== YouTube URL → embed (helper for edit preview)
function toEmbed(url) {
    const m = String(url || '').match(/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([\w\-]{6,})/i);
    return m ? `https://www.youtube.com/embed/${m[1]}` : '';
}

// ===== Edit
document.querySelectorAll('.open-edit').forEach(btn => {
    btn.addEventListener('click', () => {
        isEditing = true;

        const slug = btn.dataset.slug;
        const desc = btn.dataset.description || '';
        const mtype = btn.dataset.mediaType || '';
        const mpath = btn.dataset.mediaPath || '';
        const murl = btn.dataset.mediaUrl || '';

        hadExistingMedia = Boolean((mtype && (mpath || murl)));

        modalTitle.textContent = 'Edit Post';
        pmSubmitBtn.textContent = 'Save';

        pmDesc.value = desc;
        deriveTitle();

        pmForm.setAttribute('action', "{{ url('/posts') }}/" + slug);
        let methodField = pmForm.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            pmForm.appendChild(methodField);
        }
        methodField.value = 'PUT';

        pmExisting.innerHTML = '';
        pmExisting.classList.add('hidden');
        pmRemoveMedia.value = '0';
        pmMediaUrl.value = '';
        pmFile.value = '';
        pmPreview.innerHTML = '';
        clearInvalid();

        if (mtype === 'image' && mpath) {
            pmExisting.innerHTML =
                `<img src="{{ asset('storage') }}/${mpath}" class="w-full max-h-[280px] object-cover rounded-xl">`;
            pmExisting.classList.remove('hidden');
            setMode('image');
        } else if (mtype === 'video') {
            if (murl) {
                const embed = toEmbed(murl);
                pmExisting.innerHTML =
                    `<div class="media-frame aspect-video"><iframe src="${embed}" class="w-full h-full" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe></div>`;
                pmExisting.classList.remove('hidden');
                setMode('video');
            } else if (mpath) {
                pmExisting.innerHTML =
                    `<video src="{{ asset('storage') }}/${mpath}" controls class="w-full max-h-[280px] rounded-xl"></video>`;
                pmExisting.classList.remove('hidden');
                setMode('image');
            }
        } else {
            // no existing media
            setMode('image'); // encourage adding a file
        }

        openModal();
    });
});

// ===== Guard: media required rules
pmForm?.addEventListener('submit', (e) => {
    clearInvalid();

    const fileSelected = hasFile();
    const urlProvided = hasUrl();
    const removeFlag = pmRemoveMedia.value === '1';

    // Can't choose both (keep your guard)
    if (!removeFlag && fileSelected && urlProvided) {
        e.preventDefault();
        showToast('Pick image OR URL');
        markInvalid(); // highlight something
        return;
    }

    // If URL given, validate it's a YouTube URL we can embed
    if (urlProvided && !validYouTube()) {
        e.preventDefault();
        showToast('Enter a valid YouTube URL');
        markInvalid('url');
        return;
    }

    if (!isEditing) {
        // CREATE: must provide image OR URL
        if (!fileSelected && !urlProvided) {
            e.preventDefault();
            showToast('Media is required to post');
            markInvalid(); // highlight active area
            return;
        }
        return; // valid
    }

    // EDIT:
    // Case A: user clicked "Remove media" -> must provide NEW file or URL
    if (removeFlag && !fileSelected && !urlProvided) {
        e.preventDefault();
        showToast('Media is required to post');
        markInvalid();
        return;
    }

    // Case B: post originally had NO media, and still none provided
    if (!hadExistingMedia && !fileSelected && !urlProvided) {
        e.preventDefault();
        showToast('Media is required to post');
        markInvalid();
        return;
    }

    // Else: valid – either keeping existing media, or adding new one
});

// ===== Share (kept as-is, now uses center toast)
(function wireShare() {
    const buttons = document.querySelectorAll('.share-btn');
    if (!buttons.length) return;

    async function doShare(url) {
        try {
            if (navigator.share && window.isSecureContext) {
                await navigator.share({
                    title: document.title,
                    url
                });
                return true;
            }
        } catch (_) {}

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(url);
                showToast('Link copied');
                return true;
            }
        } catch (_) {}

        try {
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
            return true;
        } catch (_) {}

        window.prompt('Copy link', url);
        return true;
    }

    buttons.forEach((btn) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const url = this.dataset.url || window.location.href;
            doShare(url);
        }, {
            passive: false
        });
    });
})();

(function syncHeaderHeight() {
    function set() {
        const h = document.querySelector('header.glass')?.offsetHeight || 84;
        document.documentElement.style.setProperty('--header-h', h + 'px');
    }
    window.addEventListener('load', set);
    window.addEventListener('resize', set);
})();

(function enhanceDescriptions() {
    const MAX_WORDS = 250; // initial word cap
    const LINE_CLAMP = 4; // initial line cap

    const blocks = document.querySelectorAll('.post-desc');
    if (!blocks.length) return;

    // --- helpers ---
    const escapeHTML = (s) =>
        String(s)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");

    const linkify = (text) => {
        // https://…, http://…, or www….
        const urlRe = /\b((https?:\/\/|www\.)[^\s<]+[^\s<\.)])/gi;
        return text.replace(urlRe, (m) => {
            const href = m.startsWith('http') ? m : 'https://' + m;
            return `<a href="${href}" target="_blank" rel="noopener noreferrer" class="underline break-words">${m}</a>`;
        });
    };

    const nl2br = (s) => s.replace(/\n/g, '<br>');

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
        const words = s.trim().split(/\s+/);
        if (words.length <= max) return [s, false];
        return [words.slice(0, max).join(' ') + '…', true];
    };

    const decodeData = (el) => {
        const raw = el.dataset.full ?? '';
        try {
            // Quick heuristic: if it starts with a quote or contains \uXXXX, parse it.
            if (raw[0] === '"' || /\\u[0-9a-fA-F]{4}/.test(raw)) {
                return JSON.parse(raw);
            }
        } catch (_) {}
        return raw;
    };

    // --- render each description ---
    blocks.forEach((el) => {
        const container = el.parentElement; // wraps the button
        const btn = container.querySelector('.readmore-btn');

        const fullText = decodeData(el); // <- fixes \uXXXX
        const [truncText, wasWordTrimmed] = truncateWords(fullText, MAX_WORDS);

        const render = (txt) => {
            // Escape + linkify
            let safe = escapeHTML(txt);

            // Collapse double newlines to single (so no big gaps)
            safe = safe.replace(/\n{2,}/g, '\n');

            // Convert newlines into <br>
            safe = nl2br(linkify(safe));

            return safe;
        };

        // Initial paint: truncated text + clamp to 4 lines
        el.innerHTML = render(truncText);
        applyClamp(el, true);

        // Decide if toggle is needed (word-trimmed OR visually over 4 lines)
        let needsToggle = wasWordTrimmed;

        requestAnimationFrame(() => {
            // Measure full height
            const temp = document.createElement('div');
            temp.className = el.className;
            temp.style.position = 'absolute';
            temp.style.visibility = 'hidden';
            temp.style.width = el.clientWidth + 'px';
            temp.innerHTML = render(fullText);
            document.body.appendChild(temp);
            const fullH = temp.scrollHeight;
            document.body.removeChild(temp);

            const clampedH = el.getBoundingClientRect().height;
            if (fullH > clampedH) needsToggle = true;

            if (needsToggle && btn) {
                btn.classList.remove('hidden');
                let expanded = false;

                btn.addEventListener('click', () => {
                    expanded = !expanded;
                    if (expanded) {
                        el.innerHTML = render(fullText);
                        applyClamp(el, false);
                        btn.textContent = 'Read less';
                    } else {
                        el.innerHTML = render(truncText);
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