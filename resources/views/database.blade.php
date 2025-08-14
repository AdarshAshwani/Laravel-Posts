<!DOCTYPE html>
<html lang="en">

<head>
    <title>Posts Installer - Database Setup</title>
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
                <span class="progress-dot active"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <div class="ml-5 flex gap-5 text-xs text-blue-400 font-bold opacity-80">
                    <span>Welcome</span>
                    <span class="active">Database</span>
                    <span>Admin</span>
                    <span>Finish</span>
                </div>
            </div>
            <!-- Headline -->
            <h2 class="text-3xl sm:text-4xl font-extrabold mb-3 text-gray-900 leading-tight">Database Setup</h2>
            <p class="text-gray-500 mb-5 text-lg font-medium">
                Enter your database credentials.<br>
                <span class="text-gray-600">We'll instantly test your connection and guide you to the next step.</span>
            </p>
            <!-- Show error if any -->
            @if($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-600">
                {{ $errors->first() }}
            </div>
            @endif
            <!-- Form -->
            <form method="POST" action="{{ route('install.database.save') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">DB Host</label>
                    <input type="text" name="db_host" required placeholder="e.g., 127.0.0.1"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">DB Name</label>
                    <input type="text" name="db_name" required placeholder="e.g., posts_db"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">DB Username</label>
                    <input type="text" name="db_user" required placeholder="e.g., root"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">DB Password</label>
                    <input type="password" name="db_pass" placeholder="Your DB password"
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-200 px-4 py-2 shadow-sm transition" />
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('install') }}"
                        class="text-gray-400 hover:text-blue-600 font-semibold transition">Back</a>
                    <button type="submit"
                        class="glow bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-2 px-8 rounded-full shadow-lg transition transform hover:scale-105 focus:outline-none">
                        Next
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loader Modal -->
    <div id="db-loader-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm transition-all duration-300 opacity-0 pointer-events-none">
        <div class="bg-white/90 glassy rounded-2xl shadow-2xl px-10 py-8 flex flex-col items-center min-w-[350px]">
            <!-- Animated DB Icon -->
            <svg class="w-16 h-16 mb-4 animate-bounce" fill="none" viewBox="0 0 60 60">
                <ellipse cx="30" cy="15" rx="18" ry="7" fill="#6366f1" fill-opacity="0.10" />
                <ellipse cx="30" cy="15" rx="18" ry="7" fill="#6366f1" fill-opacity="0.17" />
                <rect x="12" y="15" width="36" height="20" rx="10" fill="#6366f1" fill-opacity="0.14" />
                <ellipse cx="30" cy="35" rx="18" ry="7" fill="#6366f1" fill-opacity="0.18" />
                <rect x="12" y="35" width="36" height="12" rx="8" fill="#6366f1" fill-opacity="0.13" />
                <ellipse cx="30" cy="47" rx="18" ry="7" fill="#6366f1" fill-opacity="0.20" />
                <ellipse cx="30" cy="47" rx="14" ry="4" fill="#6366f1" fill-opacity="0.21" />
            </svg>
            <!-- Progress Bar -->
            <div class="w-full mb-2">
                <div class="relative w-full h-3 rounded-full bg-blue-100 overflow-hidden">
                    <div id="loader-bar"
                        class="absolute left-0 top-0 h-full bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-400 rounded-full transition-all"
                        style="width: 0%"></div>
                </div>
            </div>
            <!-- Status Text -->
            <div id="loader-status" class="mt-2 text-blue-700 font-bold text-lg tracking-wide text-center">
                Setting up your database...
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loaderModal = document.getElementById('db-loader-modal');
        const loaderBar = document.getElementById('loader-bar');
        const loaderStatus = document.getElementById('loader-status');
        const form = document.querySelector('form[action*="install.database.save"]');
        let loadingInterval = null;
        let progress = 0;

        function showLoader() {
            loaderModal.classList.add('show');
            loaderBar.style.width = '0%';
            loaderStatus.textContent = "Setting up your database...";
            progress = 0;
            if (loadingInterval) clearInterval(loadingInterval);

            loadingInterval = setInterval(() => {
                if (progress < 93) {
                    progress += Math.random() * 8 + 2;
                    loaderBar.style.width = Math.min(progress, 93) + '%';
                }
            }, 320);

            setTimeout(() => {
                loaderBar.style.width = "100%";
                loaderStatus.textContent = "Finalizing installation...";
            }, 1400);

            setTimeout(() => {
                loaderBar.style.width = "100%";
                loaderStatus.textContent = "Almost done...";
            }, 1800);
        }

        function hideLoader() {
            loaderModal.classList.remove('show');
            loaderBar.style.width = "0%";
            if (loadingInterval) clearInterval(loadingInterval);
        }


        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                showLoader();
                setTimeout(() => {
                    form.submit();
                }, 2000); // Keep loader visible for at least 2 seconds
            });

            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('beforeunload', hideLoader);
        }
    });
    </script>
</body>

</html>