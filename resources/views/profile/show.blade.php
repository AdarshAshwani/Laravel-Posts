@extends('layouts.app')

@section('title','Profile')
@section('page_title','My Profile')

{{-- Solid modal skins (prevents transparency in dark) --}}
@push('styles')
<style>
.modal--solid {
    background-color: #ffffff;
    color: #0f172a;
    border: 1px solid rgba(226, 232, 240, .7);
}

.dark .modal--solid {
    background-color: #0b1220;
    color: #e2e8f0;
    border-color: rgba(30, 41, 59, .7);
}

.modal__backdrop {
    background: rgba(2, 6, 23, .55);
}

.modal__panel {
    border-radius: 1rem;
}
</style>
@endpush

@section('page_actions')
<a href="{{ route('admin.dashboard') }}" class="btn btn-ghost inline-flex items-center gap-2">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M15 19l-7-7 7-7" />
    </svg>
    Back
</a>
@endsection

@section('content')
@php
// fallback to config app.name if setting is empty
$siteName = setting('site_name', config('app.name', 'Posts'));
@endphp
@php
$social = $user->social_links ?? [];
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
'website' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0 12
-2.7-2.2-2.7-9.8 0-12zm-6 6h12M6 14h12',
];
$labels = [
'facebook' => 'Facebook', 'twitter' => 'X / Twitter', 'youtube'=>'YouTube',
'wordpress'=>'WordPress','instagram'=>'Instagram','quora'=>'Quora',
'pinterest'=>'Pinterest','linkedin'=>'LinkedIn','blogger'=>'Blogger',
'website'=>'Website',
];
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    {{-- HEADER --}}
    <section class="surface-strong soft-card p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div class="relative">
                <div
                    class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60 overflow-hidden">
                    @if($user->avatar_path)
                    <img src="{{ asset('storage/'.$user->avatar_path) }}?v={{ optional($user->updated_at)->timestamp }}"
                        alt="Avatar" class="w-full h-full object-cover">
                    @endif
                </div>
                <button class="btn-ghost text-xs absolute -bottom-2 left-1/2 -translate-x-1/2"
                    data-modal-open="modal-avatar">Change</button>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold truncate">{{ $user->name ?? $user->username }}</h1>
                    @if($user->is_admin)
                    <span
                        class="px-2 py-0.5 text-xs rounded-full bg-emerald-600/10 text-emerald-700 dark:text-emerald-300">Admin</span>
                    @endif
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 truncate">
                    {{ '@'.$user->username }} · {{ $user->email }}
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button class="btn btn-primary" data-modal-open="modal-edit">Edit profile</button>
                    <button class="btn btn-primary" data-modal-open="modal-password">Change password</button>
                    <button class="btn btn-primary"
                        data-modal-open="modal-social">{{ empty($social) ? 'Add social links' : 'Edit social links' }}</button>
                    @if(class_exists(\App\Models\Subprofile::class))
                    <button class="btn btn-primary" data-modal-open="modal-add-sub">Add profile</button>
                    @endif
                    @if(auth()->user()?->is_admin)
                    <button class="btn btn-primary" data-modal-open="modal-branding">Site branding</button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- OVERVIEW --}}
    <section class="surface soft-card p-6">
        <h2 class="text-lg font-bold">Account Overview</h2>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border border-slate-200/70 dark:border-slate-800/70">
                <div class="text-xs uppercase tracking-wide text-slate-500">Name</div>
                <div class="mt-1 font-semibold">{{ $user->name ?: '—' }}</div>
            </div>
            <div class="p-4 rounded-xl border border-slate-200/70 dark:border-slate-800/70">
                <div class="text-xs uppercase tracking-wide text-slate-500">Username</div>
                <div class="mt-1 font-semibold">{{ $user->username }}</div>
            </div>
            <div class="p-4 rounded-xl border border-slate-200/70 dark:border-slate-800/70">
                <div class="text-xs uppercase tracking-wide text-slate-500">Email</div>
                <div class="mt-1 font-semibold">{{ $user->email }}</div>
            </div>
            <div class="p-4 rounded-xl border border-slate-200/70 dark:border-slate-800/70">
                <div class="text-xs uppercase tracking-wide text-slate-500">Role</div>
                <div class="mt-1 font-semibold">{{ $user->is_admin ? 'Administrator' : 'Member' }}</div>
            </div>
        </div>
    </section>

    @php
    $social = $user->social_links ?? [];

    // icons (same keys as controller validation)
    $icons = [
    'facebook' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3V2z',
    'twitter' => 'M22 5.8c-.7.3-1.5.5-2.2.6.8-.5 1.4-1.2 1.7-2.1-.8.5-1.7.8-2.6 1A4 4 0 0012 8.5c0 .3 0 .6.1.9A11.3 11.3
    0 013 5.1a4 4 0 001.2 5.3c-.6 0-1.1-.2-1.6-.4v.1a4 4 0 003.2 3.9c-.5.2-1 .2-1.6.1a4 4 0 003.7 2.8A8.1 8.1 0 012 18.6
    11.5 11.5 0 008.3 20c7 0 10.9-5.8 10.9-10.9v-.5c.8-.5 1.5-1.2 2-2z',
    'youtube' => 'M10 15l5.2-3L10 9v6z M21.6 7.2c.2.8.4 2 .4 4s-.2 3.2-.4 4c-.2.8-.8 1.4-1.6 1.6C18.6 17 12 17 12
    17s-6.6 0-8-.2a2.2 2.2 0 01-1.6-1.6C2.2 14.4 2 13.2 2 11.2s.2-3.2.4-4C2.6 6.4 3.2 5.8 4 5.6 5.4 5.4 12 5.4 12
    5.4s6.6 0 8 .2c.8.2 1.4.8 1.6 1.6z',
    'wordpress' => 'M4 12a8 8 0 1016 0A8 8 0 004 12zm13.7-1.1a6.7 6.7 0 01-2.1 6.2l-2.3-6.2 1.1-3 3.3 3zM8.7 7.9c.5 0
    .8.2 1 .6l2.4 6.5-1.3 3.3A6.7 6.7 0 017.1 8c.4-.1 1-.1 1.6-.1zm6 .1c.5 0 1 .1 1.3.2.4.8.6 1.6.6 2.6 0 1-.4 2.1-1
    3.4l-1.8-5 .9-1.2z',
    'instagram' => 'M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5a5 5 0 100 10 5 5 0
    000-10zm6-1a1 1 0 110 2 1 1 0 010-2z',
    'quora' => 'M12 2a8 8 0 100 16 7.9 7.9 0 005.8-2.5l1.9 2.2 1.4-1.2-2-2.3A7.8 7.8 0 0020 10c0-4.4-3.6-8-8-8zm0 3a5 5
    0 015 5c0 2.6-2 4.7-4.5 5l1.3 1.6h-2.1l-1.1-1.4A5 5 0 017 10a5 5 0 015-5z',
    'pinterest' => 'M12 2a10 10 0 00-3.6 19.3c-.1-.8-.2-2 .1-2.8l2-8s-.5-1 0-1.6c.6-.7 1.7-.5 2.3 0 .7.6.6 1.7.4
    2.6l-1.3 5a3.5 3.5 0 005.9-2.8c0-3.2-2.3-5.4-5.6-5.4-3.8 0-6.1 2.8-6.1 5.8 0 1.4.5 2.9 1.6
    3.7.2.2.3.1.4-.1l.4-1.6c0-.2 0-.3-.1-.5a3.3 3.3 0 01-.1-2.3c.4-1.2 1.7-2 3-2 2 0 3.3 1.4 3.3 3.3 0 2.3-1.1 3.9-2.8
    3.9-.9 0-1.6-.7-1.4-1.6l.4-1.5',
    'linkedin' => 'M4 3a2 2 0 012-2h12a2 2 0 012 2v18a2 2 0 01-2 2H6a2 2 0 01-2-2V3zm3 6h3v10H7V9zm1.5-5a1.7 1.7 0 100
    3.4 1.7 1.7 0 000-3.4zM12 9h3v1.5c.6-.9 1.6-1.7 3-1.7 3 0 3.5 2 3.5 4.6V19h-3v-4.3c0-1.1 0-2.4-1.5-2.4s-1.7 1.2-1.7
    2.3V19H12V9z',
    'blogger' => 'M6 3h8a5 5 0 015 5v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8a5 5 0 015-5zm2 4a2 2 0 100 4h4a2 2 0 100-4H8zm0 6a2
    2 0 100 4h6a2 2 0 100-4H8z',
    'website' => 'M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0
    12-2.7-2.2-2.7-9.8 0-12zM6 12h12M6 14h12',
    ];

    // brand color pairs (used for gradients & glow)
    $brand = [
    'facebook' => ['#02142dff','#0E5AD1'],
    'twitter' => ['#031d2eff','#0C8BD6'],
    'youtube' => ['#450101ff','#D40000'],
    'wordpress' => ['#032738ff','#125E7F'],
    'instagram' => ['#5f2d04ff','#DD2A7B'], // warm → magenta
    'quora' => ['#420402ff','#8C1E1B'],
    'pinterest' => ['#49010cff','#B8001C'],
    'linkedin' => ['#022343ff','#004182'],
    'blogger' => ['#5d2604ff','#E26314'],
    'website' => ['#00313aff','#3B82F6'], // teal → blue
    ];
    @endphp

    <section class="surface soft-card p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold">Social Profiles</h2>
            <button class="btn" data-modal-open="modal-social">
                {{ empty($social) ? 'Add links' : 'Edit links' }}
            </button>
        </div>

        <div class="social-grid">
            @php
            // Map keys → Simple Icons slugs (keeps both twitter & x if you ever add it)
            $slugMap = [
            'facebook'=>'facebook', 'twitter'=>'x', 'youtube'=>'youtube', 'wordpress'=>'wordpress',
            'instagram'=>'instagram', 'quora'=>'quora', 'pinterest'=>'pinterest', 'linkedin'=>'linkedin',
            'blogger'=>'blogger', 'website'=>null,
            ];
            $labelMap = [
            'facebook'=>'Facebook','twitter'=>'X / Twitter','youtube'=>'YouTube','wordpress'=>'WordPress',
            'instagram'=>'Instagram','quora'=>'Quora','pinterest'=>'Pinterest','linkedin'=>'LinkedIn',
            'blogger'=>'Blogger','website'=>'Website',
            ];
            // Ad-block-safe inline path for LinkedIn
            $linkedinPath = 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.447-2.136
            2.944v5.662H9.352V9h3.414v1.561h.049c.476-.9 1.637-1.852 3.368-1.852 3.602 0 4.268 2.371 4.268
            5.455v6.288zM5.337 7.433a2.062 2.062 0 11.001-4.124 2.062 2.062 0 01-.001 4.124zM6.96
            20.452H3.713V9H6.96v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.728v20.543C0 23.226.792 24 1.771 24h20.451C23.2
            24 24 23.226 24 22.271V1.728C24 .774 23.2 0 22.222 0h.003z';
            @endphp

            @foreach($icons as $key => $unusedPath) {{-- we ignore the old inline path --}}
            @php
            $slug = $slugMap[$key] ?? null;
            $label = $labelMap[$key] ?? ucfirst($key);
            $url = $social[$key] ?? null;
            [$c1,$c2] = $brand[$key] ?? ['#0f172a','#334155'];

            // white glyph for active; slate-400 for empty (better in light mode)
            $emptyHex = '94a3b8';
            $imgSrc = $slug ? "https://cdn.simpleicons.org/{$slug}/" . ($url ? 'ffffff' : $emptyHex) : null;
            @endphp

            @if($url)
            <a href="{{ $url }}" target="_blank" rel="noopener" class="social-pill is-active tooltip"
                style="--brand-start: {{ $c1 }}; --brand-end: {{ $c2 }};" data-tip="{{ $label }}"
                aria-label="{{ $label }}">
                @if($slug === 'linkedin')
                <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor" d="{{ $linkedinPath }}" />
                </svg>
                @elseif($imgSrc)
                <img src="{{ $imgSrc }}" alt="" class="icon" loading="lazy" decoding="async" />
                @else
                {{-- generic globe for website --}}
                <svg class="icon glyph" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="currentColor"
                        d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 110 16A8 8 0 0112 4zm0 0c2.7 2.2 2.7 9.8 0 12-2.7-2.2-2.7-9.8 0-12zM6 12h12M6 14h12" />
                </svg>
                @endif
                <span class="sr-only">{{ $label }}</span>
            </a>
            @else
            <button type="button" class="social-pill is-empty tooltip" data-modal-open="modal-social"
                data-tip="Add {{ $label }}" aria-label="Add {{ $label }}">
                @if($slug === 'linkedin')
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
                <span class="sr-only">Add {{ $label }}</span>
            </button>
            @endif
            @endforeach
        </div>


        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
            Tip: Click a grey icon to add that link.
        </p>
    </section>

    {{-- SUB-PROFILES (unchanged) --}}
    @if(class_exists(\App\Models\Subprofile::class))
    <section class="surface soft-card p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold">Profiles</h2>
            <button class="btn" data-modal-open="modal-add-sub">Add profile</button>
        </div>
        <div class="mt-4 divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse($user->subprofiles as $sp)
            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <div class="font-semibold truncate">
                        {{ $sp->label }}
                        @if($sp->is_default)
                        <span
                            class="ml-2 px-2 py-0.5 text-xs rounded-full bg-blue-600/10 text-blue-700 dark:text-blue-300">Default</span>
                        @endif
                    </div>
                    @if($sp->bio)
                    <div class="text-sm text-slate-500 dark:text-slate-400">{{ $sp->bio }}</div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @unless($sp->is_default)
                    <form method="POST" action="{{ route('profiles.default',$sp) }}">@csrf @method('PATCH')
                        <button class="btn-ghost text-sm">Make Default</button>
                    </form>
                    @endunless
                    <form method="POST" action="{{ route('profiles.destroy',$sp) }}"
                        onsubmit="return confirm('Remove this profile?')">
                        @csrf @method('DELETE')
                        <button class="btn-ghost text-sm text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500 dark:text-slate-400">No profiles yet.</div>
            @endforelse
        </div>
    </section>
    @endif
</div>

{{-- ==================== MODALS ==================== --}}
{{-- Edit Profile --}}
<div class="modal" id="modal-edit" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Edit profile</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" id="form-edit">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Name (optional)</label>
                    <input class="input" name="name" value="{{ old('name',$user->name) }}">
                    @error('name') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Username</label>
                    <input class="input" name="username" value="{{ old('username',$user->username) }}" required>
                    @error('username') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Email</label>
                    <input class="input" type="email" name="email" value="{{ old('email',$user->email) }}" required>
                    @error('email') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Change Password --}}
<div class="modal" id="modal-password" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Change password</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4" id="form-password">
            @csrf @method('PUT')
            <div>
                <label class="label">Current Password</label>
                <input class="input" type="password" name="current_password" required>
                @error('current_password') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">New Password</label>
                <input class="input" type="password" name="password" required>
                @error('password') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Confirm Password</label>
                <input class="input" type="password" name="password_confirmation" required>
            </div>
            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Update password</button>
            </div>
        </form>
    </div>
</div>

{{-- Change Avatar --}}
<div class="modal" id="modal-avatar" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Change avatar</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>
        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="space-y-4"
            id="form-avatar">
            @csrf
            <div>
                <label class="label">Upload image</label>
                <input type="file" name="avatar" accept="image/*" class="input">
                <p class="text-xs text-slate-500 mt-1">Accepted: JPG, PNG, WEBP. Max 2 MB.</p>
                @error('avatar') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Save avatar</button>
            </div>
        </form>
    </div>
</div>

{{-- Social Links --}}
<div class="modal" id="modal-social" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Social links</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>

        <form method="POST" action="{{ route('profile.social') }}" class="space-y-4" id="form-social">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="label">Website</label>
                    <input class="input" name="website" type="url" placeholder="https://example.com"
                        value="{{ old('website', $social['website'] ?? '') }}">
                    @error('website') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">Facebook</label>
                    <input class="input" name="facebook" type="url" placeholder="https://facebook.com/username"
                        value="{{ old('facebook', $social['facebook'] ?? '') }}">
                    @error('facebook') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">X / Twitter</label>
                    <input class="input" name="twitter" type="url" placeholder="https://twitter.com/handle"
                        value="{{ old('twitter', $social['twitter'] ?? '') }}">
                    @error('twitter') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">YouTube</label>
                    <input class="input" name="youtube" type="url" placeholder="https://youtube.com/@channel"
                        value="{{ old('youtube', $social['youtube'] ?? '') }}">
                    @error('youtube') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">WordPress</label>
                    <input class="input" name="wordpress" type="url" placeholder="https://yourblog.wordpress.com"
                        value="{{ old('wordpress', $social['wordpress'] ?? '') }}">
                    @error('wordpress') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">Instagram</label>
                    <input class="input" name="instagram" type="url" placeholder="https://instagram.com/username"
                        value="{{ old('instagram', $social['instagram'] ?? '') }}">
                    @error('instagram') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">Quora</label>
                    <input class="input" name="quora" type="url" placeholder="https://www.quora.com/profile/You"
                        value="{{ old('quora', $social['quora'] ?? '') }}">
                    @error('quora') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">Pinterest</label>
                    <input class="input" name="pinterest" type="url" placeholder="https://pinterest.com/username"
                        value="{{ old('pinterest', $social['pinterest'] ?? '') }}">
                    @error('pinterest') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">LinkedIn</label>
                    <input class="input" name="linkedin" type="url" placeholder="https://www.linkedin.com/in/you"
                        value="{{ old('linkedin', $social['linkedin'] ?? '') }}">
                    @error('linkedin') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div><label class="label">Blogger</label>
                    <input class="input" name="blogger" type="url" placeholder="https://yourblog.blogspot.com"
                        value="{{ old('blogger', $social['blogger'] ?? '') }}">
                    @error('blogger') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <p class="text-xs text-slate-500">Leave any field blank to hide that icon.</p>

            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Site Branding --}}
<div class="modal" id="modal-branding" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Edit your Site Branding</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>

        <form class="space-y-4" method="POST" action="{{ route('settings.branding.update') }}"
            enctype="multipart/form-data" id="form-branding">
            @csrf
            <div>
                <label class="label">Site name</label>
                <input name="site_name" class="input" value="{{ old('site_name', $siteName) }}" required maxlength="30">
                @error('site_name') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Sub-Profile (unchanged from your version) --}}
@if(class_exists(\App\Models\Subprofile::class))
<div class="modal" id="modal-add-sub" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__panel modal--solid shadow-2xl">
        <div class="modal__header">
            <h3 class="text-lg font-bold">Add profile</h3>
            <button class="modal__close" data-modal-close aria-label="Close">✕</button>
        </div>
        <form method="POST" action="{{ route('profiles.store') }}" class="space-y-4" id="form-add-sub">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Label</label>
                    <input class="input" name="label" placeholder="e.g., Work" required>
                    @error('label') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Bio (optional)</label>
                    <input class="input" name="bio" placeholder="Short description">
                    @error('bio') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="modal__footer">
                <button type="button" class="btn-ghost" data-modal-close>Cancel</button>
                <button class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== Modal core styles & script (unchanged) ===== --}}
<style>
.modal {
    position: fixed;
    inset: 0;
    display: none;
    z-index: 50;
}

.modal[aria-hidden="false"] {
    display: block;
}

.modal__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(2, 6, 23, .4);
}

.modal__panel {
    position: relative;
    margin: 6rem auto 2rem auto;
    max-width: 42rem;
    width: calc(100% - 2rem);
    border: 1px solid rgba(226, 232, 240, .7);
}

.dark .modal__panel {
    border-color: rgba(30, 41, 59, .7)
}

.modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(226, 232, 240, .7)
}

.dark .modal__header {
    border-color: rgba(30, 41, 59, .7)
}

.modal__close {
    opacity: .7
}

.modal__close:hover {
    opacity: 1
}

.modal__panel form {
    padding: 1rem 1.25rem
}

.modal__footer {
    display: flex;
    justify-content: flex-end;
    gap: .5rem;
    margin-top: .5rem
}
</style>

<script>
(() => {
    const qs = (sel, el = document) => el.querySelector(sel);
    const qsa = (sel, el = document) => Array.from(el.querySelectorAll(sel));
    let lastActive = null;

    function openModal(id) {
        const modal = qs('#' + id);
        if (!modal) return;
        lastActive = document.activeElement;
        modal.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => {
            const focusable = modal.querySelector(
                'button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])');
            focusable?.focus();
        });
        document.documentElement.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.setAttribute('aria-hidden', 'true');
        document.documentElement.style.overflow = '';
        lastActive?.focus();
    }

    qsa('[data-modal-open]').forEach(btn => btn.addEventListener('click', () => openModal(btn.getAttribute(
        'data-modal-open'))));
    qsa('[data-modal-close]').forEach(el => el.addEventListener('click', () => {
        const modal = el.closest('.modal');
        if (modal) closeModal(modal);
    }));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') qsa('.modal[aria-hidden="false"]').forEach(m => closeModal(m));
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Tab') return;
        const open = qs('.modal[aria-hidden="false"]');
        if (!open) return;
        const focusables = qsa('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])', open)
            .filter(el => !el.hasAttribute('disabled'));
        if (!focusables.length) return;
        const [first, last] = [focusables[0], focusables[focusables.length - 1]];
        if (e.shiftKey && document.activeElement === first) {
            last.focus();
            e.preventDefault();
        } else if (!e.shiftKey && document.activeElement === last) {
            first.focus();
            e.preventDefault();
        }
    });

    // Auto-open modal on validation error (includes social fields)
    @if($errors->any())
    const formsToModal = {
        'name': 'modal-edit',
        'username': 'modal-edit',
        'email': 'modal-edit',
        'current_password': 'modal-password',
        'password': 'modal-password',
        'password_confirmation': 'modal-password',
        'site_name': 'modal-branding',
        'avatar': 'modal-avatar',
        'website': 'modal-social',
        'facebook': 'modal-social',
        'twitter': 'modal-social',
        'youtube': 'modal-social',
        'wordpress': 'modal-social',
        'instagram': 'modal-social',
        'quora': 'modal-social',
        'pinterest': 'modal-social',
        'linkedin': 'modal-social',
        'blogger': 'modal-social',
        'label': 'modal-add-sub',
        'bio': 'modal-add-sub',
    };
    const firstErrorField = '{{ array_key_first($errors->toArray()) }}';
    const target = formsToModal[firstErrorField];
    if (target) openModal(target);
    @endif
})();
</script>
@endsection