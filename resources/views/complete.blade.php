<!DOCTYPE html>
<html lang="en">
<head>
    <title>Posts Installer - Installation Complete</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
    <style>
      @keyframes float {
        0%,100% {transform:translateY(0);}
        50% {transform:translateY(-12px);}
      }
      .float {animation: float 4s cubic-bezier(.52,.14,.47,1.18) infinite;}
      .glassy {
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        background: rgba(255,255,255,0.96);
        border: 1.5px solid #e0e7ef36;
        box-shadow: 0 10px 40px #6366f130, 0 1.5px 5px #3b82f115;
      }
      .progress-dot {
        width: 11px; height: 11px;
        background: #c7d2fe;
        border-radius: 50%;
        margin-right: 8px;
        transition: background 0.3s;
        box-shadow: 0 0 0 0 transparent;
      }
      .progress-dot.active {
        background: linear-gradient(90deg, #22c55e 30%, #3b82f6 100%);
        box-shadow: 0 0 0 5px #22c55e44;
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
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-200 via-blue-100 to-blue-200 relative overflow-hidden">
    <!-- Decorative BG Blobs -->
    <div class="absolute top-[-12vw] left-[-12vw] w-[38vw] h-[38vw] bg-green-300 bg-opacity-25 rounded-full blur-3xl z-0 animate-pulse"></div>
    <div class="absolute bottom-[-8vw] right-[-8vw] w-[32vw] h-[32vw] bg-blue-300 bg-opacity-20 rounded-full blur-2xl z-0 animate-pulse"></div>

    <div class="glassy relative z-10 w-full max-w-4xl mx-auto rounded-3xl shadow-2xl p-0 overflow-hidden flex flex-col md:flex-row">
        <!-- Illustration/Brand Panel -->
        <div class="hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-green-50 via-blue-50 to-blue-100 w-2/5 p-10">
            <div class="mb-2 mt-2 text-xl text-blue-700 font-bold text-center">Posts One-Click Installer</div>
            <svg class="float" width="120" height="80" viewBox="0 0 120 80" fill="none">
                <ellipse cx="60" cy="40" rx="38" ry="38" fill="#22c55e" fill-opacity="0.08"/>
                <ellipse cx="60" cy="40" rx="28" ry="28" stroke="#22c55e" stroke-width="6" fill="#fff"/>
                <path d="M52 41l8 8 13-13" stroke="#22c55e" stroke-width="5" stroke-linecap="round"/>
                <ellipse cx="60" cy="40" rx="7" ry="7" fill="#22c55e"/>
            </svg>
            <div class="text-green-500 mt-4 text-xs opacity-80 tracking-wide text-center">Installation Success!</div>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-center px-8 py-10 sm:px-12 items-center">
            <!-- Progress Dots -->
            <div class="flex items-center mb-7">
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot active"></span>
                <div class="ml-5 flex gap-5 text-xs text-blue-400 font-bold opacity-80">
                    <span>Welcome</span>
                    <span>Database</span>
                    <span>Admin</span>
                    <span>Finish</span>
                </div>
            </div>
            <!-- Checkmark Celebration -->
            <div class="flex flex-col items-center mb-6">
                <svg class="w-20 h-20 text-green-400 mb-4 animate-pulse" fill="none" viewBox="0 0 48 48" stroke="currentColor">
                  <circle cx="24" cy="24" r="22" stroke-width="3" class="stroke-green-300"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M16 25l6 6 12-12" class="stroke-green-500"/>
                </svg>
                <h1 class="text-3xl font-bold mb-2 text-gray-800 text-center">Installation Complete!</h1>
                <p class="text-gray-500 mb-4 text-center">
                    🎉 Congratulations, your application is ready to go.<br>
                    Click below to access your site.
                </p>
            </div>
            <a href="/admin"
               class="glow bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-bold py-2 px-8 rounded-full shadow-lg mb-2 transition transform hover:scale-105 focus:outline-none">
                Go to Admin Dashboard
            </a>
            <a href="/"
               class="text-blue-600 hover:underline mt-3 font-semibold transition">Visit Public Site</a>
        </div>
    </div>
</body>
</html>
