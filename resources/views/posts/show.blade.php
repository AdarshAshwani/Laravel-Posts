{{-- resources/views/posts/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $post->title ?? 'Post' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* ========= THEME TOKENS ========= */
    :root{
      /* Light theme */
      --bg:        #f6f8fc;
      --card:      #ffffff;
      --text:      #0f172a;   /* slate-900 */
      --muted:     #64748b;   /* slate-500 */
      --brand:     #2563eb;   /* blue-600 */
      --border:    #e2e8f0;   /* slate-200 */
      --btn:       #ffffff;
      --btnText:   #0f172a;
      --btnHover:  #f1f5f9;   /* slate-100 */
      --toast:     #0f172a;   /* dark toast on light */
      --toastText: #ffffff;
      --ring:      rgba(37,99,235,.25);
    }
    .dark:root{
      /* Dark theme */
      --bg:        #0b1220;
      --card:      #121a2b;
      --text:      #e6e9f0;
      --muted:     #9aa3b2;
      --brand:     #6b7cff;
      --border:    #1f2a44;
      --btn:       #16223a;
      --btnText:   #e6e9f0;
      --btnHover:  #1b2948;
      --toast:     #1a2338;
      --toastText: #e9eefb;
      --ring:      #2a3a62;
    }

    /* ========= BASE ========= */
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; color:var(--text); font:16px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif;
      background:
        radial-gradient(1000px 500px at 120% 10%, rgba(99,102,241,.06), transparent 60%),
        radial-gradient(1200px 600px at 10% -10%, rgba(59,130,246,.06), transparent 60%),
        var(--bg);
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    a{color:inherit}

    .wrap{max-width:780px;margin:28px auto;padding:0 16px}

    /* top bar (back + theme toggle) */
    .bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
    .back{display:inline-flex;align-items:center;gap:8px;color:var(--muted);text-decoration:none}
    .back:hover{color:color-mix(in oklab, var(--muted), #fff 20%)}

    .toggle{
      padding:7px 10px;border-radius:10px;border:1px solid var(--border);
      background:var(--btn);color:var(--btnText);cursor:pointer;font-size:12px;
      box-shadow:0 6px 16px -10px rgba(2,6,23,.18);
    }
    .toggle:hover{background:var(--btnHover);outline:0;border-color:var(--ring)}

    /* Card */
    .card{
      background:var(--card);
      border:1px solid var(--border);
      border-radius:18px;
      box-shadow:0 1px 2px rgba(2,6,23,.08), 0 12px 36px -24px rgba(2,6,23,.45);
    }
    .pad{padding:22px}
    .row{display:flex;gap:14px;align-items:flex-start}
    .avatar{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#6a7bff,#2dd4bf)}
    h1{font-size:18px;margin:0}
    .meta{color:var(--muted);font-size:13px;margin-top:2px}
    .desc{white-space:pre-line;margin-top:10px;color:color-mix(in oklab, var(--text), #fff 8%)}

    /* Media */
    .media{margin-top:14px;border-radius:14px;overflow:hidden;background:#000}
    .media img,.media video{width:100%;display:block;max-height:560px;object-fit:cover}
    .media a.inline-image{display:block;line-height:0}
    .media iframe{width:100%;aspect-ratio:16/9;border:0;display:block;background:#000}

    /* Actions */
    .actions{display:flex;justify-content:flex-end;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)}
    .btn{padding:10px 14px;border-radius:999px;border:1px solid var(--border);background:var(--btn);color:var(--btnText);cursor:pointer}
    .btn:hover{background:var(--btnHover);border-color:var(--ring)}

    /* Toast (center-bottom) */
    .toast{
      position:fixed;left:50%;bottom:24px;transform:translateX(-50%) translateY(16px);
      background:var(--toast);color:var(--toastText);
      border:1px solid var(--border);padding:12px 16px;border-radius:12px;
      box-shadow:0 10px 30px rgba(2,6,23,.35);
      opacity:0;pointer-events:none;transition:opacity .25s ease,transform .25s ease;z-index:60;
      display:flex;align-items:center;gap:8px;font-weight:600
    }
    .toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    .toast .dot{width:8px;height:8px;border-radius:50%;background:var(--brand);box-shadow:0 0 0 4px color-mix(in oklab, var(--brand), transparent 82%)}

    /* Lightbox */
    .lightbox{position:fixed;inset:0;z-index:70;display:none}
    .lightbox.show{display:block}
    .lb-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.8);backdrop-filter:saturate(140%) blur(2px)}
    .lb-body{position:relative;height:100%;display:flex;align-items:center;justify-content:center;padding:24px}
    .lb-img{max-width:min(96vw,1600px);max-height:90vh;border-radius:12px;box-shadow:0 10px 50px rgba(0,0,0,.6)}
    .lb-close{position:absolute;top:18px;right:18px;border:1px solid var(--border);background:var(--btn);
      color:var(--btnText);border-radius:999px;padding:8px 11px;cursor:pointer}
    .lb-close:hover{background:var(--btnHover);border-color:var(--ring)}

    .link{color:var(--brand);text-decoration:none}
    .link:hover{text-decoration:underline}
  </style>

  <!-- No-flicker dark boot -->
  <script>
    (function () {
      const ls = localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (ls === 'dark' || (!ls && prefersDark)) document.documentElement.classList.add('dark');
    })();
  </script>
</head>
<body>
  <div class="wrap">
    <div class="bar">
      <a class="back" href="{{ url('/') }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M15 18l-6-6 6-6"/></svg>
        Back
      </a>
      <button id="themeToggle" class="toggle" type="button">Toggle Theme</button>
    </div>

    <article class="card">
      <header class="pad row">
        <div class="avatar" aria-hidden="true"></div>
        <div>
          <h1>{{ $post->user->username ?? $post->user->email ?? 'User' }}</h1>
          <div class="meta">{{ $post->created_at?->diffForHumans() }}</div>
          @if($post->description)
            <div class="desc">{{ $post->description }}</div>
          @endif
        </div>
      </header>

      {{-- Media --}}
      @if($post->media->isNotEmpty())
        @php
          $m = $post->media->first();
          $youtubeUrl = $m->youtube_url ?? null;
          if (!$youtubeUrl && preg_match('~^https?://(www\.)?(youtube\.com|youtu\.be)/~i', $m->file_path)) {
              $youtubeUrl = $m->file_path;
          }
          $ytId = null;
          if ($youtubeUrl && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w\-]{6,})~i', $youtubeUrl, $matches)) {
              $ytId = $matches[1];
          }
        @endphp
        <div class="media">
          @if($m->media_type === 'image')
            <a href="{{ asset('storage/'.$m->file_path) }}"
               class="inline-image"
               data-full="{{ asset('storage/'.$m->file_path) }}"
               title="Open image">
              <img src="{{ asset('storage/'.$m->file_path) }}" alt="Post image">
            </a>
          @elseif($m->media_type === 'video' && $ytId)
            <iframe src="https://www.youtube-nocookie.com/embed/{{ $ytId }}?rel=0&modestbranding=1"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
          @elseif($m->media_type === 'video')
            <video controls playsinline src="{{ asset('storage/'.$m->file_path) }}"></video>
          @elseif($m->media_type === 'url')
            <div class="pad">
              <a href="{{ $m->file_path }}" target="_blank" rel="noopener" class="link">{{ $m->file_path }}</a>
            </div>
          @endif
        </div>
      @endif

      <div class="pad actions">
        <button id="shareBtn" class="btn" type="button">Share</button>
      </div>
    </article>
  </div>

  <!-- Toast -->
  <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true">
    <span class="dot"></span>
    <span id="toastText">Link copied</span>
  </div>

  <!-- Image Lightbox -->
  <div id="lightbox" class="lightbox" aria-hidden="true">
    <div class="lb-backdrop" data-close></div>
    <div class="lb-body">
      <img class="lb-img" id="lbImg" alt="Full-size image">
      <button class="lb-close" type="button" data-close aria-label="Close">✕</button>
    </div>
  </div>

  <script>
    // Theme toggle (persisted)
    document.getElementById('themeToggle')?.addEventListener('click', () => {
      const html = document.documentElement;
      const isDark = html.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });

    // Toast
    const toast = document.getElementById('toast');
    const toastText = document.getElementById('toastText');
    let toastTimer;
    function showToast(msg='Done') {
      toastText.textContent = msg;
      toast.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(()=> toast.classList.remove('show'), 1600);
    }

    // Share (Web Share API + clipboard fallback)
    document.getElementById('shareBtn')?.addEventListener('click', async () => {
      const url = window.location.href;
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
      ta.value = url; ta.style.position='fixed'; ta.style.opacity='0'; ta.style.left='-9999px';
      document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
      showToast('Link copied');
    });

    // Lightbox (open on image click; close on backdrop/close/ESC)
    const lb = document.getElementById('lightbox');
    const lbImg = document.getElementById('lbImg');

    document.querySelector('.media a.inline-image')?.addEventListener('click', (e) => {
      e.preventDefault();
      const full = e.currentTarget.getAttribute('data-full') || e.currentTarget.href;
      lbImg.src = full;
      lb.style.display = 'block';
      requestAnimationFrame(()=> lb.classList.add('show'));
      lb.setAttribute('aria-hidden','false');
    });

    function closeLB(){
      lb.classList.remove('show');
      lb.setAttribute('aria-hidden','true');
      setTimeout(()=>{ lb.style.display='none'; lbImg.removeAttribute('src'); }, 150);
    }
    lb?.addEventListener('click', (e)=>{ if (e.target.hasAttribute('data-close')) closeLB(); });
    document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') closeLB(); });
  </script>
</body>
</html>
