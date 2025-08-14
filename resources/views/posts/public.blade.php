@extends('layouts.app')

@section('title', 'All Posts')
@section('page_title', 'Public Posts')

{{-- No-flicker dark mode boot (skip if your layout already has it) --}}
@section('head')
<script>
  (function () {
    const ls = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (ls === 'dark' || (!ls && prefersDark)) document.documentElement.classList.add('dark');
  })();
</script>
@endsection

@section('content')
{{-- Theme toggle like on index --}}
<div class="max-w-3xl mx-auto mb-3 flex items-center justify-end">
  <button id="themeToggle"
          class="text-xs px-3 py-1.5 rounded-lg border bg-white/80 hover:bg-white
                 dark:bg-slate-700 dark:hover:bg-slate-600
                 border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 transition">
    Toggle Theme
  </button>
</div>

<div class="max-w-3xl mx-auto space-y-6">

  @forelse($posts as $post)
    {{-- Use the SAME card class set as index.blade.php --}}
    <article class="soft-card soft-card-hover spotlight p-5">
      <header class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60"></div>
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <h3 class="font-bold">
              <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">
                {{ $post->user->username ?? $post->user->email ?? 'User' }}
              </a>
            </h3>
            <span class="text-xs text-slate-400">•</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>
          </div>
          @if($post->description)
            {{-- Same readable body colors as index --}}
            <p class="mt-2 text-slate-700 dark:text-slate-200 whitespace-pre-line leading-7">
              {{ $post->description }}
            </p>
          @endif
        </div>
      </header>

      {{-- Media (reuse media-frame like index) --}}
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
              $ytUrl = $m->youtube_url ?? null;
              $ytId = null;
              if ($ytUrl && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/)|youtu\.be/)([\w\-]{6,})~i', $ytUrl, $mm)) {
                $ytId = $mm[1];
              }
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
                <video controls playsinline src="{{ asset('storage/'.$m->file_path) }}" class="w-full max-h-[560px] object-cover"></video>
              </div>
            @elseif($thumb && $ytUrl)
              <a href="{{ $ytUrl }}" target="_blank" rel="noopener" class="block media-frame">
                <img src="{{ $thumb }}" alt="Watch on YouTube" class="w-full object-cover">
              </a>
            @endif

          @elseif($m->media_type === 'url')
            <div class="p-4">
              <a href="{{ $m->file_path }}" target="_blank" class="text-indigo-600 dark:text-indigo-300 hover:underline">
                {{ $m->file_path }}
              </a>
            </div>
          @endif
        </div>
      @endif

      {{-- Actions (match index divider & ghost buttons) --}}
      <footer class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between gap-3">
        <a href="{{ route('posts.show', $post->slug) }}"
           class="btn-ghost text-sm">View</a>

        <button type="button"
                class="btn-ghost text-sm share-btn"
                data-url="{{ route('posts.show', $post->slug) }}">
          Share
        </button>
      </footer>
    </article>
  @empty
    <div class="soft-card text-slate-600 dark:text-slate-300">
      No posts yet.
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

{{-- Center toast (same look as index) --}}
<div id="toast" class="toast">
  <span class="inline-block w-2 h-2 rounded-full bg-blue-400 shadow-[0_0_0_4px_rgba(59,130,246,.18)]"></span>
  <span id="toastText" class="text-sm font-semibold">Link copied</span>
</div>

<script>
  // Theme toggle
  document.getElementById('themeToggle')?.addEventListener('click', () => {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });

  // Center toast helper (same as index)
  function showToast(msg='Done') {
    const t  = document.getElementById('toast');
    const tx = document.getElementById('toastText');
    if (!t || !tx) return;
    tx.textContent = msg;
    t.classList.add('is-visible');
    clearTimeout(t._t);
    t._t = setTimeout(()=>t.classList.remove('is-visible'), 1600);
  }

  // Share
  document.querySelectorAll('.share-btn').forEach(btn=>{
    btn.addEventListener('click', async () => {
      const url = btn.dataset.url;
      try {
        if (navigator.share && window.isSecureContext) {
          await navigator.share({ title: document.title, url });
          return;
        }
      } catch(_) {}
      try {
        if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(url);
          showToast('Link copied');
          return;
        }
      } catch(_) {}
      const ta = document.createElement('textarea');
      ta.value = url; ta.setAttribute('readonly','');
      ta.style.position='fixed'; ta.style.opacity='0'; ta.style.left='-9999px';
      document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
      showToast('Link copied');
    });
  });
</script>
@endsection
