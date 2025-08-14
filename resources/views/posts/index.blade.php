@extends('layouts.app')

@section('title','Posts')
@section('page_title','Community Feed')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Compact launcher --}}
    <button id="openCreate" class="composer-launch">
        <div
            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60 flex items-center justify-center text-white font-bold">
            +</div>
        <div class="flex-1 text-slate-500 dark:text-slate-400">Start a post…</div>
        <div class="btn btn-primary text-sm">Create</div>
    </button>

    {{-- Feed --}}
    @forelse($posts as $post)
    @php $m = $post->media->first(); @endphp
    <article class="soft-card soft-card-hover spotlight p-5">
        <header class="flex items-start gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60">
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-bold">{{ $post->user->username ?? $post->user->email }}</h3>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                @if($post->description)
                <p class="mt-1 text-slate-700 dark:text-slate-200 whitespace-pre-line leading-7">
                    {{ $post->description }}</p>
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
            $yt = $m->youtube_url ?? null;
            $embed = null;
            if ($yt && preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w\-]{6,})~i', $yt, $mm))
            {
            $embed = 'https://www.youtube.com/embed/'.$mm[1];
            }
            @endphp
            @if($embed)
            <div class="media-frame aspect-video">
                <iframe src="{{ $embed }}" class="w-full h-full" allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
            </div>
            @elseif($m->file_path)
            <div class="media-frame">
                <video class="w-full max-h-[520px] object-cover" src="{{ asset('storage/'.$m->file_path) }}" controls
                    playsinline></video>
            </div>
            @endif
            @endif
        </div>
        @endif

        {{-- Actions --}}
        <footer class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-800/60">
            <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-300">
                {{-- Edit --}}
                <button type="button" {{-- prevent unintended submit --}}
                    class="flex items-center gap-1 hover:text-blue-600 open-edit" data-slug="{{ $post->slug }}"
                    data-description="{{ e($post->description) }}" data-media-type="{{ optional($m)->media_type }}"
                    data-media-path="{{ optional($m)->file_path }}" data-media-url="{{ optional($m)->youtube_url }}">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.83z" />
                    </svg>
                    Edit
                </button>

                {{-- Delete --}}
                <form method="POST" action="{{ route('posts.destroy', $post->slug) }}"
                    onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-1 hover:text-rose-600">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                        </svg>
                        Delete
                    </button>
                </form>

                {{-- Share --}}
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
    <div class="soft-card text-center text-slate-500">No posts yet. Be the first to share!</div>
    @endforelse

    <div class="mt-6">{{ $posts->links() }}</div>
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
</script>

@endsection