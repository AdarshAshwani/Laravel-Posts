<!DOCTYPE html>
<html lang="en">

<head>
    <title>Posts Installer - Admin Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
    <style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-12px);
        }
    }

    .float {
        animation: float 4s cubic-bezier(.52, .14, .47, 1.18) infinite;
    }

    .glassy {
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        background: rgba(255, 255, 255, 0.92);
        border: 1.5px solid #e0e7ef36;
        box-shadow: 0 10px 40px #6366f130, 0 1.5px 5px #3b82f115;
    }

    .progress-dot {
        width: 11px;
        height: 11px;
        background: #c7d2fe;
        border-radius: 50%;
        margin-right: 8px;
        transition: background 0.3s;
        box-shadow: 0 0 0 0 transparent;
    }

    .progress-dot.active {
        background: linear-gradient(90deg, #6366f1 30%, #3b82f6 100%);
        box-shadow: 0 0 0 5px #3b82f631;
    }

    .divider {
        background: linear-gradient(90deg, #c7d2fe 0%, #fff 40%, #fff 60%, #c7d2fe 100%);
        height: 2px;
        width: 100%;
        margin: 24px 0;
        border-radius: 1px;
    }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-blue-100 via-indigo-100 to-blue-50 relative overflow-hidden">
    <!-- Decorative BG Blobs -->
    <div
        class="absolute top-[-12vw] left-[-12vw] w-[38vw] h-[38vw] bg-blue-300 bg-opacity-25 rounded-full blur-3xl z-0 animate-pulse">
    </div>
    <div
        class="absolute bottom-[-8vw] right-[-8vw] w-[32vw] h-[32vw] bg-indigo-300 bg-opacity-20 rounded-full blur-2xl z-0 animate-pulse">
    </div>
    <div
        class="glassy relative z-10 w-full max-w-4xl mx-auto rounded-3xl shadow-2xl p-0 overflow-hidden flex flex-col md:flex-row">
        <!-- Illustration/Brand Panel -->
        <div
            class="hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 w-2/5 p-10">

            <div class="mb-2 mt-2 text-xl text-blue-700 font-bold text-center">Posts One-Click Installer</div>
            <svg class="float" width="120" height="80" viewBox="0 0 120 80" fill="none">
                <ellipse cx="60" cy="40" rx="38" ry="38" fill="#6366f1" fill-opacity="0.07" />
                <ellipse cx="60" cy="40" rx="28" ry="28" stroke="#3b82f6" stroke-width="6" fill="#fff" />
                <path d="M48 56l12 12 12-12" stroke="#6366f1" stroke-width="5" stroke-linecap="round" />
                <ellipse cx="60" cy="40" rx="7" ry="7" fill="#6366f1" />
            </svg>
            <div class="text-blue-500 mt-4 text-xs opacity-80 tracking-wide text-center">AI-powered Setup Wizard</div>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-center px-8 py-10 sm:px-12">
            <!-- Progress Dots -->
            <div class="flex items-center mb-7">
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot active"></span>
                <span class="progress-dot"></span>
                <div class="ml-5 flex gap-5 text-xs text-blue-400 font-bold opacity-80">
                    <span>Welcome</span>
                    <span>Database</span>
                    <span>Admin</span>
                    <span>Finish</span>
                </div>
            </div>
            <!-- Headline -->
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-3 text-gray-900 leading-tight">Admin Account</h2>
            <p class="text-gray-500 mb-5 text-lg font-medium">
                Set up your admin account to manage the site.
            </p>
            <!-- Show error if any -->
            @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-600">
                {{ $errors->first() }}
            </div>
            @endif
            <!-- Form -->
            <form method="POST" action="{{ route('install.admin') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" required placeholder="Admin username"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="you@email.com"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        placeholder="Create a password"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        autocomplete="new-password" placeholder="Confirm password"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <!-- Password match feedback, styled -->
                <div id="password-match-message"
                    class="flex items-center gap-2 mt-2 min-h-[24px] text-base font-semibold transition-all"></div>
                <div class="divider"></div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('install.database') }}"
                        class="text-gray-400 hover:text-blue-600 font-semibold transition">Back</a>
                    <button type="submit"
                        class="glow bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-2 px-8 rounded-full shadow-lg transition transform hover:scale-105 focus:outline-none">
                        Finish Setup
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const message = document.getElementById('password-match-message');
    const submitBtn = document.querySelector('button[type="submit"]');

    let confirmInteracted = false;

    function getMatchIcon(matched) {
        return matched
          ? `<svg class="w-5 h-5 text-green-500 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" fill="#d1fae5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12l2 2 4-4"/></svg>`
          : `<svg class="w-5 h-5 text-red-500 animate-shake" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" fill="#fee2e2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6m0-6l6 6"/></svg>`;
    }

    // Add shake animation for error icon (if not already present)
    if (!document.getElementById('shake-anim-style')) {
        const style = document.createElement('style');
        style.id = 'shake-anim-style';
        style.innerHTML = `
          @keyframes shake { 0%, 100% { transform: translateX(0); } 20%, 60% { transform: translateX(-5px); } 40%, 80% { transform: translateX(5px); } }
          .animate-shake { animation: shake 0.45s; }
        `;
        document.head.appendChild(style);
    }

    function showMatchMessage() {
        if (!confirmInteracted) {
            message.innerHTML = '';
            submitBtn.disabled = false;
            return;
        }
        if (!password.value && !confirm.value) {
            message.innerHTML = '';
            submitBtn.disabled = false;
            return;
        }
        if (password.value === confirm.value) {
            if (password.value.length < 6) {
                message.innerHTML = `${getMatchIcon(false)}<span class="text-red-500">Password must be at least 6 characters</span>`;
                submitBtn.disabled = true;
            } else {
                message.innerHTML = `${getMatchIcon(true)}<span class="text-green-600">Passwords match</span>`;
                submitBtn.disabled = false;
            }
        } else {
            message.innerHTML = `${getMatchIcon(false)}<span class="text-red-500">Passwords do not match</span>`;
            submitBtn.disabled = true;
        }
    }

    // Mark as interacted ONLY after the first change in confirm field
    confirm.addEventListener('input', function () {
        confirmInteracted = true;
        showMatchMessage();
    });
    // Always check on password change, but only show if user has already interacted with confirm field
    password.addEventListener('input', showMatchMessage);

    // Always re-check on form submit (so if they skip confirm field, still blocks submit)
    const form = password.closest('form');
    if (form) {
        form.addEventListener('submit', function(e){
            confirmInteracted = true;
            showMatchMessage();
            if (submitBtn.disabled) e.preventDefault();
        });
    }
});
</script>
