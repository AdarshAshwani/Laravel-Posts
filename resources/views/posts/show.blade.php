@extends('layouts.app')

@section('title', $post->title ?? 'Post')
@section('page_title', 'Post')

@section('page_actions')
  <a href="{{ url()->previous() ?: route('posts.public') }}" class="btn btn-ghost inline-flex items-center gap-2">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15 19l-7-7 7-7"/></svg>
    Back
  </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <article class="soft-card soft-card-hover spotlight p-5">
    {{-- Header --}}
    <header class="flex items-start gap-3">
      @php $author = $post->user; @endphp
      @if($author && $author->avatar_path)
        <img
          src="{{ asset('storage/'.$author->avatar_path) }}?v={{ optional($author->updated_at)->timestamp }}"
          alt="{{ $author->username ?? $author->email }} avatar"
          class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/60 dark:ring-slate-900/60"
          loading="lazy" decoding="async">
      @else
        <div
          class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60
                 flex items-center justify-center text-white text-sm font-semibold">
          {{ strtoupper(substr($author->username ?? $author->email ?? 'U', 0, 1)) }}
        </div>
      @endif

      <div class="flex-1">
        <div class="flex items-center gap-2">
          <h1 class="font-bold">{{ $author->username ?? $author->email ?? 'User' }}</h1>
          <span class="text-xs text-slate-400">•</span>
          <span class="text-xs text-slate-500 dark:text-slate-400">{{ $post->created_at?->diffForHumans() }}</span>
        </div>

        @if($post->description)
          <div class="mt-2 text-slate-400 dark:text-slate-500 leading-7">
            <div class="post-desc break-words" data-full='@json($post->description)'></div>
            <button type="button" class="readmore-btn text-sm font-semibold text-blue-600 hover:underline mt-1 hidden">
              Read more
            </button>
          </div>
        @endif
      </div>
    </header>

    {{-- Media --}}
    @if($post->media->isNotEmpty())
      @php
        $m = $post->media->first();
        $youtubeUrl = $m->youtube_url ?? null;
        if (!$youtubeUrl && isset($m->file_path) && preg_match('~^https?://(www\.)?(youtube\.com|youtu\.be)/~i', $m->file_path)) {
          $youtubeUrl = $m->file_path;
        }
        $ytId = null;
        if ($youtubeUrl && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w\-]{6,})~i', $youtubeUrl, $matches)) {
          $ytId = $matches[1];
        }
      @endphp

      <div class="mt-4 media-frame">
        @if($m->media_type === 'image' && $m->file_path)
          <a href="{{ asset('storage/'.$m->file_path) }}" class="block inline-image" data-full="{{ asset('storage/'.$m->file_path) }}">
            <img src="{{ asset('storage/'.$m->file_path) }}" alt="Post image" class="w-full max-h-[560px] object-cover rounded-xl">
          </a>

        @elseif($m->media_type === 'video' && $ytId)
          <div class="aspect-video rounded-xl overflow-hidden bg-black">
            <iframe
              src="https://www.youtube-nocookie.com/embed/{{ $ytId }}?rel=0&modestbranding=1"
              class="w-full h-full"
              allowfullscreen
              referrerpolicy="strict-origin-when-cross-origin"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
          </div>

        @elseif($m->media_type === 'video' && $m->file_path)
          <video controls playsinline src="{{ asset('storage/'.$m->file_path) }}" class="w-full max-h-[560px] object-cover rounded-xl"></video>

        @elseif($m->media_type === 'url')
          <div class="p-4">
            <a href="{{ $m->file_path }}" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-300 hover:underline">
              {{ $m->file_path }}
            </a>
          </div>
        @endif
      </div>
    @endif

    {{-- Actions --}}
    <footer class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center justify-end">
      <button id="shareBtn" class="btn btn-icon" type="button">Share</button>
    </footer>
  </article>
</div>

{{-- Lightbox --}}
<div id="lightbox" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/80 backdrop-blur-[2px]" data-close></div>
  <div class="relative h-full flex items-center justify-center p-6">
    <img id="lbImg" class="max-w-[96vw] max-h-[90vh] rounded-xl shadow-2xl" alt="Full-size image">
    <button class="absolute top-4 right-4 btn-ghost" type="button" data-close aria-label="Close">✕</button>
  </div>
</div>

{{-- Toast (reuse your .toast styles if present) --}}
<div id="toast" class="toast">
  <span class="inline-block w-2 h-2 rounded-full bg-blue-400 shadow-[0_0_0_4px_rgba(59,130,246,.18)]"></span>
  <span id="toastText" class="text-sm font-semibold">Link copied</span>
</div>
@endsection

@push('page_scripts')
<script>
  // Share
  document.getElementById('shareBtn')?.addEventListener('click', async () => {
    const url = window.location.href;
    try {
      if (navigator.share && window.isSecureContext) {
        await navigator.share({ title: document.title, url });
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
    ta.setAttribute('readonly','');
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select(); document.execCommand('copy'); ta.remove();
    showToast('Link copied');
  });

  // Center toast helper (same as your index/public pages)
  function showToast(msg = 'Done') {
    const t = document.getElementById('toast');
    const tx = document.getElementById('toastText');
    if (!t || !tx) return;
    tx.textContent = msg;
    t.classList.add('is-visible');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove('is-visible'), 1600);
  }

  // Lightbox
  (function(){
    const lb = document.getElementById('lightbox');
    const lbImg = document.getElementById('lbImg');
    document.querySelector('.inline-image')?.addEventListener('click', (e) => {
      e.preventDefault();
      const full = e.currentTarget.getAttribute('data-full') || e.currentTarget.href;
      lbImg.src = full;
      lb.classList.remove('hidden');
    });
    lb?.addEventListener('click', (e) => { if (e.target.hasAttribute('data-close')) { lb.classList.add('hidden'); lbImg.removeAttribute('src'); }});
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { lb.classList.add('hidden'); lbImg.removeAttribute('src'); }});
  })();

  // Description “Read more” (match other pages)
  (function enhanceDescription() {
    const MAX_WORDS = 250, LINE_CLAMP = 4;
    const block = document.querySelector('.post-desc');
    if (!block) return;
    const btn = document.querySelector('.readmore-btn');

    const escapeHTML = (s) => String(s)
      .replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
      .replace(/"/g,"&quot;").replace(/'/g,"&#39;");
    const linkify = (text) => text.replace(/\b((https?:\/\/|www\.)[^\s<]+[^\s<\.)])/gi, (m) => {
      const href = m.startsWith('http') ? m : 'https://' + m;
      return `<a href="${href}" target="_blank" rel="noopener noreferrer" class="underline break-words">${m}</a>`;
    });
    const nl2br = (s) => s.replace(/\n/g,'<br>');
    const applyClamp = (el, on) => {
      if (on) { el.style.display='-webkit-box'; el.style.webkitBoxOrient='vertical'; el.style.webkitLineClamp=String(LINE_CLAMP); el.style.overflow='hidden'; }
      else { el.style.display=''; el.style.webkitBoxOrient=''; el.style.webkitLineClamp=''; el.style.overflow=''; }
    };
    const truncateWords = (s,max) => {
      const words = s.trim().split(/\s+/); if (words.length <= max) return [s,false];
      return [words.slice(0,max).join(' ') + '…', true];
    };
    const decodeData = (el) => {
      const raw = el.dataset.full ?? ''; try { if (raw[0]==='"' || /\\u[0-9a-fA-F]{4}/.test(raw)) return JSON.parse(raw); } catch(_) {}
      return raw;
    };
    const render = (txt) => {
      let safe = escapeHTML(txt);
      safe = safe.replace(/\n{2,}/g,'\n');
      return nl2br(linkify(safe));
    };

    const fullText = decodeData(block);
    const [truncText, wasTrimmed] = truncateWords(fullText, MAX_WORDS);
    block.innerHTML = render(truncText); applyClamp(block,true);

    let needsToggle = wasTrimmed;
    requestAnimationFrame(() => {
      const tmp = document.createElement('div');
      tmp.className = block.className; tmp.style.position='absolute'; tmp.style.visibility='hidden';
      tmp.style.width = block.clientWidth + 'px'; tmp.innerHTML = render(fullText);
      document.body.appendChild(tmp); const fullH = tmp.scrollHeight; document.body.removeChild(tmp);
      const clampedH = block.getBoundingClientRect().height;
      if (fullH > clampedH) needsToggle = true;

      if (needsToggle && btn) {
        btn.classList.remove('hidden');
        let expanded = false;
        btn.addEventListener('click', () => {
          expanded = !expanded;
          if (expanded) { block.innerHTML = render(fullText); applyClamp(block,false); btn.textContent='Read less'; }
          else { block.innerHTML = render(truncText); applyClamp(block,true); btn.textContent='Read more'; }
        });
      }
    });
  })();
</script>
@endpush
