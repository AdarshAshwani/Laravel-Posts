<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])

    <style>
    /* motion + glass */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-10px)
        }
    }

    .float {
        animation: float 4s cubic-bezier(.52, .14, .47, 1.18) infinite
    }

    .glassy {
        backdrop-filter: blur(18px) saturate(170%);
        -webkit-backdrop-filter: blur(18px) saturate(170%);
        background: rgba(255, 255, 255, .94);
        border: 1.5px solid #e0e7ef36;
        box-shadow: 0 10px 40px #6366f130, 0 1.5px 5px #3b82f115
    }

    .divider {
        background: linear-gradient(90deg, #c7d2fe 0%, #fff 40%, #fff 60%, #c7d2fe 100%);
        height: 2px;
        width: 100%;
        margin: 24px 0;
        border-radius: 1px
    }

    /* Dark polish for card + header/footer sections */
    .dark .glassy {
        background: rgba(2, 6, 23, .78);
        border-color: #1f293733
    }

    .dark .card-header {
        background: linear-gradient(135deg, #0b1220 0%, #0f172a 100%)
    }

    .light-header {
        background: linear-gradient(135deg, #eef2ff 0%, #dbeafe 100%)
    }

    /* Inputs (autofill + invalid) */
    input::placeholder {
        color: rgb(148 163 184)
    }

    /* slate-400 */
    .dark input::placeholder {
        color: rgb(100 116 139)
    }

    /* slate-500 */
    input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px rgb(255 255 255) inset !important;
        -webkit-text-fill-color: rgb(15 23 42) !important;
        caret-color: rgb(15 23 42) !important;
    }

    .dark input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px rgb(30 41 59) inset !important;
        /* slate-800 */
        -webkit-text-fill-color: rgb(226 232 240) !important;
        /* slate-200 */
        caret-color: rgb(226 232 240) !important;
    }

    .is-invalid {
        outline: 2px solid rgba(239, 68, 68, .3) !important;
        outline-offset: 2px
    }

    /* Center toast pattern (if you reuse later) */
    .toast-center {
        position: fixed;
        inset: auto 0 auto 0;
        top: 12px;
        margin: auto;
        width: max-content;
        max-width: 90vw;
        z-index: 60;
        transform: translateY(-12px);
        opacity: 0;
        pointer-events: none;
        transition: transform .18s ease, opacity .18s ease;
    }

    .toast-center.is-visible {
        transform: translateY(0);
        opacity: 1
    }

    @media (prefers-reduced-motion: reduce) {
        .float {
            animation: none
        }
    }
    </style>

    <!-- Dark mode boot (no flicker) -->
    <script>
    (function() {
        const ls = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (ls === 'dark' || (!ls && prefersDark)) document.documentElement.classList.add('dark');
    })();
    </script>
</head>

<body class="min-h-screen flex items-center justify-center
             bg-gradient-to-tr from-blue-100 via-indigo-100 to-blue-50
             dark:from-slate-900 dark:via-slate-900 dark:to-slate-900
             relative overflow-hidden text-slate-900 dark:text-slate-100">

    <!-- Decorative blobs (hide on very small screens) -->
    <div
        class="hidden sm:block absolute -top-[12vw] -left-[12vw] w-[38vw] h-[38vw] bg-blue-300/20 dark:bg-indigo-500/10 rounded-full blur-3xl z-0 animate-pulse">
    </div>
    <div
        class="hidden sm:block absolute -bottom-[8vw] -right-[8vw] w-[32vw] h-[32vw] bg-indigo-300/15 dark:bg-indigo-400/10 rounded-full blur-2xl z-0 animate-pulse">
    </div>

    <!-- Card -->
    <div
        class="glassy dark:bg-slate-900/70 dark:border-slate-800 relative z-10 w-[92%] sm:w-11/12 md:w-full max-w-md mx-auto rounded-3xl shadow-2xl overflow-hidden">

        <!-- Header -->
        <div
            class="card-header light-header dark:from-slate-800 dark:to-slate-800 px-5 sm:px-6 md:px-7 py-4 sm:py-5 flex items-center justify-between">
            <div>
                <div class="text-base sm:text-lg text-blue-700 dark:text-indigo-300 font-extrabold">Admin Login</div>
                <div class="text-blue-600/80 dark:text-slate-400 mt-0.5 text-[11px] sm:text-xs tracking-wide">Sign in to
                    continue</div>
            </div>
            <button id="themeToggle"
                class="text-[11px] sm:text-xs px-3 py-1.5 rounded-lg bg-white/80 hover:bg-white
                     dark:bg-slate-700 dark:hover:bg-slate-600 border border-slate-200/70 dark:border-slate-600 transition"
                aria-pressed="false">
                Toggle Theme
            </button>
        </div>

        <!-- Body -->
        <div class="px-5 sm:px-6 md:px-7 py-6 sm:py-7">
            {{-- Success / info --}}
            @if(session('message'))
            <div class="alert alert-success" role="status">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm-1 14-4-4 1.414-1.414L11 12.172l4.586-4.586L17 9l-6 7z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
            <div class="alert alert-error" role="alert">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 13h-2v2h2v-2zm0-8h-2v6h2V7z" />
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                    <div class="relative">
                        <input type="email" name="email" required value="{{ old('email') }}" autocomplete="username"
                            class="w-full rounded-xl border border-gray-300 dark:border-slate-700
                          bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100
                          placeholder-slate-400 dark:placeholder-slate-500
                          focus:border-blue-500 dark:focus:border-indigo-500
                          focus:ring-2 focus:ring-blue-500/30 dark:focus:ring-indigo-500/30
                          px-4 py-2.5 shadow-sm transition" placeholder="you@email.com" autofocus>
                        <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22 6v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6l10 7L22 6zM20 4H4l8 6 8-6z" />
                        </svg>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                        <span id="capsMsg" class="hidden text-xs font-semibold text-amber-600 dark:text-amber-400">Caps
                            Lock is ON</span>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full rounded-xl border border-gray-300 dark:border-slate-700
                          bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100
                          placeholder-slate-400 dark:placeholder-slate-500
                          focus:border-blue-500 dark:focus:border-indigo-500
                          focus:ring-2 focus:ring-blue-500/30 dark:focus:ring-indigo-500/30
                          px-4 py-2.5 shadow-sm transition pr-12" placeholder="Your password">
                        <button type="button" id="togglePwd" tabindex="-1"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                            aria-label="Show password">
                            <svg id="eyeOpen" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 110-10 5 5 0 010 10z" />
                            </svg>
                            <svg id="eyeClosed" class="w-5 h-5 text-slate-400 hidden" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M3 3l18 18-1.5 1.5-3.1-3.1C14.9 21 12 21 12 21c-7 0-10-7-10-7a17 17 0 014.6-5.7L1.5 4.5 3 3zM12 7c2.8 0 5 2.2 5 5 0 .6-.1 1.1-.3 1.6L10.4 7.3c.5-.2 1-.3 1.6-.3z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-slate-600">
                        Remember me
                    </label>
                    {{-- <a href="#" class="text-xs text-blue-600 dark:text-indigo-300 hover:underline">Forgot password?</a> --}}
                </div>

                <div class="divider"></div>

                <!-- Submit -->
                <button id="submitBtn" type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-indigo-700 hover:to-blue-700
                       text-white font-bold py-2.5 px-8 rounded-full shadow-lg transition transform hover:scale-[1.02]
                       focus:outline-none focus:ring-2 focus:ring-blue-500/40 dark:focus:ring-indigo-500/40
                       text-base sm:text-lg flex items-center justify-center gap-2">
                    <svg id="spinner" class="w-5 h-5 animate-spin hidden" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="btnText">Login</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="px-5 sm:px-6 md:px-7 pb-6 text-center text-[11px] text-slate-500 dark:text-slate-500">
            © {{ date('Y') }} Posts
        </div>
    </div>

    <script>
    // Theme toggle (persists + ARIA)
    const themeBtn = document.getElementById('themeToggle');
    themeBtn?.addEventListener('click', () => {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        themeBtn.setAttribute('aria-pressed', String(isDark));
    });

    // Show/Hide password
    const pwd = document.getElementById('password');
    const toggle = document.getElementById('togglePwd');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');
    toggle?.addEventListener('click', () => {
        if (!pwd) return;
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', !show);
        eyeClosed.classList.toggle('hidden', show);
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });

    // Caps Lock detection
    const capsMsg = document.getElementById('capsMsg');

    function checkCaps(e) {
        if (!capsMsg) return;
        const caps = e.getModifierState && e.getModifierState('CapsLock');
        capsMsg.classList.toggle('hidden', !caps);
    }
    pwd?.addEventListener('keyup', checkCaps);
    pwd?.addEventListener('keydown', checkCaps);

    // Submit loading state
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const spin = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');
    form?.addEventListener('submit', () => {
        btn.setAttribute('disabled', 'true');
        btn.classList.add('opacity-80', 'cursor-wait');
        spin.classList.remove('hidden');
        btnText.textContent = 'Signing in…';
    });
    </script>
</body>

</html>