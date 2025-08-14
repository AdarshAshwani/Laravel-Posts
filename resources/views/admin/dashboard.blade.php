@extends('layouts.app')

@section('title','Posts')
@section('page_title','Community Feed')

@section('page_actions')
  <a href="{{ route('posts.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700">
    Refresh
  </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

  {{-- Composer / Create Post --}}
  <div class="rounded-2xl bg-white dark:bg-slate-800 shadow p-5">
    <form id="composerForm" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div class="flex gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 shrink-0"></div>
        <div class="flex-1">
          <textarea
            name="body"
            rows="3"
            class="w-full resize-none rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Share something with your community..."
            required></textarea>
          @error('body') <p class="text-rose-500 text-sm mt-1">{{ $message }}</p> @enderror

          {{-- Attachments --}}
          <div
            id="dropzone"
            class="mt-3 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-900/40 p-4 text-sm text-slate-500 dark:text-slate-400 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition">
            <input id="fileInput" type="file" name="images[]" accept="image/*" multiple class="hidden">
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M19 19H5V5h14v14zm2-16H3a1 1 0 00-1 1v16a1 1 0 001 1h18a1 1 0 001-1V4a1 1 0 00-1-1zM8 13l2.03 2.71L12.5 12l3.5 5H6l2-4z"/></svg>
                <span><b>Click</b> or <b>drag & drop</b> images (max 8)</span>
              </div>
              <button type="button" id="chooseBtn" class="px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600">Choose</button>
            </div>
          </div>
          <div id="previews" class="mt-3 grid grid-cols-3 gap-3"></div>
          @error('images.*') <p class="text-rose-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="submit" id="postBtn"
          class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow">
          <svg id="spinner" class="w-5 h-5 animate-spin hidden" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
            <path d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" fill="currentColor" class="opacity-75"></path>
          </svg>
          <span id="postText">Post</span>
        </button>
      </div>
    </form>
  </div>
@isset($posts)
  {{-- Feed --}}
  @forelse ($posts as $post)
    <article class="rounded-2xl bg-white dark:bg-slate-800 shadow p-5">
      <header class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600"></div>
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <h3 class="font-bold">{{ $post->user->username ?? $post->user->email }}</h3>
            <span class="text-xs text-slate-400">•</span>
            <span class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</span>
          </div>
          <p class="mt-2 text-slate-700 dark:text-slate-200 whitespace-pre-line">{{ $post->body }}</p>
        </div>
      </header>

      @if($post->images->isNotEmpty())
        {{-- responsive image grid --}}
        <div class="mt-4 grid gap-3
          @if($post->images->count()===1) grid-cols-1
          @elseif($post->images->count()===2) grid-cols-2
          @else grid-cols-2 md:grid-cols-3 @endif">
          @foreach($post->images as $img)
            <a href="{{ asset('storage/'.$img->path) }}" target="_blank" class="block group overflow-hidden rounded-xl">
              <img src="{{ asset('storage/'.$img->path) }}" alt="" class="w-full h-56 object-cover group-hover:scale-[1.02] transition">
            </a>
          @endforeach
        </div>
      @endif

      {{-- actions --}}
      <footer class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-4 text-sm text-slate-500">
          <button class="flex items-center gap-1 hover:text-indigo-600">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21h4V9H2v12zM22 9h-6.31l.95-4.57.03-.32a1 1 0 00-.29-.7L15.17 2 8.59 8.59A2 2 0 008 10v9h9a2 2 0 002-2v-5h3a1 1 0 001-1v-1a1 1 0 00-1-1z"/></svg>
            Like
          </button>
          <button class="flex items-center gap-1 hover:text-indigo-600">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 6h-2v9H7v2a1 1 0 001 1h9l4 4V7a1 1 0 00-1-1zM17 12V3a1 1 0 00-1-1H3a1 1 0 00-1 1v15l4-4h10a1 1 0 001-1z"/></svg>
            Comment
          </button>
          <button class="flex items-center gap-1 hover:text-indigo-600">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8l-6 6-6-6"/></svg>
            Share
          </button>
        </div>
      </footer>
    </article>
  @empty
    <div class="text-center text-slate-500">No posts yet. Be the first to share!</div>
  @endforelse

  @else
  <div class="text-center text-slate-500">No posts yet. Be the first to share!</div>
  @endisset

  <!-- <div class="mt-6">{{ $posts->links() }}</div> -->
</div>

{{-- Minimal JS: drag & drop + previews + submit spinner --}}
<script>
  const dz = document.getElementById('dropzone');
  const input = document.getElementById('fileInput');
  const choose = document.getElementById('chooseBtn');
  const previews = document.getElementById('previews');
  const form = document.getElementById('composerForm');
  const btn = document.getElementById('postBtn');
  const spin = document.getElementById('spinner');
  const txt = document.getElementById('postText');

  choose?.addEventListener('click', () => input.click());
  dz?.addEventListener('click', () => input.click());

  dz?.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-indigo-400'); });
  dz?.addEventListener('dragleave', () => dz.classList.remove('border-indigo-400'));
  dz?.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-indigo-400');
    if (e.dataTransfer.files?.length) {
      addFiles(e.dataTransfer.files);
    }
  });

  input?.addEventListener('change', e => addFiles(e.target.files));

  function addFiles(files){
    const max = 8;
    const current = input.files ? input.files.length : 0;
    const list = new DataTransfer();
    // keep existing
    for (let i=0; i<current; i++) list.items.add(input.files[i]);
    // add new
    for (let f of files) {
      if (!f.type.startsWith('image/')) continue;
      if (list.files.length >= max) break;
      list.items.add(f);
    }
    input.files = list.files;
    renderPreviews();
  }

  function renderPreviews(){
    previews.innerHTML = '';
    const files = input.files || [];
    for (let i=0;i<files.length;i++){
      const f = files[i];
      const url = URL.createObjectURL(f);
      const wrap = document.createElement('div');
      wrap.className = 'relative group';
      wrap.innerHTML = `
        <img src="${url}" class="w-full h-28 object-cover rounded-xl shadow">
        <button type="button" data-i="${i}"
          class="absolute top-2 right-2 bg-rose-600 text-white rounded-full p-1.5 opacity-90 hover:opacity-100 shadow">✕</button>
      `;
      previews.appendChild(wrap);
      wrap.querySelector('button').addEventListener('click', e => {
        const idx = +e.currentTarget.dataset.i;
        const dt = new DataTransfer();
        const files = input.files;
        for (let j=0;j<files.length;j++){ if (j!==idx) dt.items.add(files[j]); }
        input.files = dt.files;
        renderPreviews();
      });
    }
  }

  form?.addEventListener('submit', () => {
    btn.setAttribute('disabled','true');
    btn.classList.add('opacity-80','cursor-wait');
    spin.classList.remove('hidden');
    txt.textContent = 'Posting…';
  });
</script>
@endsection
