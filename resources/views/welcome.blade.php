<!DOCTYPE html>
<html lang="en">
<head>
    <title>Posts Installer - Welcome</title>
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
        background: rgba(255,255,255,0.92);
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
<body class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-blue-100 via-indigo-100 to-blue-50 relative overflow-hidden">
    <!-- Decorative BG Blobs -->
    <div class="absolute top-[-12vw] left-[-12vw] w-[38vw] h-[38vw] bg-blue-300 bg-opacity-25 rounded-full blur-3xl z-0 animate-pulse"></div>
    <div class="absolute bottom-[-8vw] right-[-8vw] w-[32vw] h-[32vw] bg-indigo-300 bg-opacity-20 rounded-full blur-2xl z-0 animate-pulse"></div>

    <div class="glassy relative z-10 w-full max-w-4xl mx-auto rounded-3xl shadow-2xl p-0 overflow-hidden flex flex-col md:flex-row">
        <!-- Illustration/Brand Panel -->
        <div class="hidden md:flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 w-2/5 p-10">
            <div class="mb-2 mt-2 text-xl text-blue-700 font-bold text-center">Posts One-Click Installer</div>
            <svg class="float" width="120" height="80" viewBox="0 0 120 80" fill="none">
                <ellipse cx="60" cy="40" rx="38" ry="38" fill="#6366f1" fill-opacity="0.07"/>
                <ellipse cx="60" cy="40" rx="28" ry="28" stroke="#3b82f6" stroke-width="6" fill="#fff"/>
                <path d="M48 56l12 12 12-12" stroke="#6366f1" stroke-width="5" stroke-linecap="round"/>
                <ellipse cx="60" cy="40" rx="7" ry="7" fill="#6366f1"/>
            </svg>
            <div class="text-blue-500 mt-4 text-xs opacity-80 tracking-wide text-center">AI-powered Setup Wizard</div>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col justify-center px-8 py-10 sm:px-12">
            <!-- Progress Dots -->
            <div class="flex items-center mb-7">
                <span class="progress-dot active"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <span class="progress-dot"></span>
                <div class="ml-5 flex gap-5 text-xs text-blue-400 font-bold opacity-80">
                    <span class="active">Welcome</span>
                    <span>Database</span>
                    <span>Admin</span>
                    <span>Finish</span>
                </div>
            </div>
            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl font-extrabold mb-3 text-gray-900 leading-tight">Welcome to <span class="bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-700 bg-clip-text text-transparent">Posts Installer</span></h1>
            <!-- Subtitle -->
            <p class="text-gray-500 mb-5 text-lg font-medium">
                Ready to launch <span class="text-blue-600 font-semibold underline underline-offset-2">your all-in-one Digital Asset Manager</span>?<br>
                <span class="text-gray-600">Get started in just one click—no tech skills needed!</span>
            </p>
            <!-- Feature Highlights (Grid!) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-7">
                <div class="flex items-center gap-2 text-blue-700 text-base font-semibold">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="#6366f1" stroke-width="2"/><path d="M8 12l2.5 2.5L16 9" stroke="#10b981" stroke-width="2" stroke-linecap="round"/></svg>
                    Instant setup
                </div>
                <div class="flex items-center gap-2 text-indigo-700 text-base font-semibold">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24"><path d="M12 3v18M3 12h18" stroke="#6366f1" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="10" stroke="#6366f1" stroke-width="2"/></svg>
                    Secure &amp; private
                </div>
                <div class="flex items-center gap-2 text-blue-700 text-base font-semibold">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24"><path d="M8 17l4 4 4-4" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 3v18" stroke="#6366f1" stroke-width="2" stroke-linecap="round"/></svg>
                    Cloud Ready
                </div>
                <div class="flex items-center gap-2 text-indigo-700 text-base font-semibold">
                    <svg width="19" height="19" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="#6366f1" stroke-width="2"/><path d="M12 8v4l2.5 2.5" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"/></svg>
                    24/7 Support
                </div>
            </div>
            <!-- Divider -->
            <div class="divider"></div>
            <!-- CTA Button -->
            <a href="{{ route('install.database') }}"
               class="glow bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-10 rounded-full text-lg flex items-center gap-2 shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 mb-5">
                <svg width="23" height="23" fill="none" viewBox="0 0 23 23"><path d="M3 12l7 7 7-7M10 18V3h3v15" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Start Installation
            </a>
        </div>
    </div>
</body>
</html>
