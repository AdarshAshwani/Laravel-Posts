<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', config('app.name', 'Posts'))</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    (function(){
      const ls = localStorage.getItem('theme');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (ls === 'dark' || (!ls && prefersDark)) document.documentElement.classList.add('dark');
    })();
  </script>
</head>
<body class="h-full">
  <div id="drawer-backdrop" class="fixed inset-0 z-30 bg-slate-900/40 hidden"></div>

  <div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar" class="hidden lg:flex lg:fixed lg:inset-y-0 lg:left-0 lg:w-68 lg:h-screen overflow-y-auto lg:flex-col shrink-0 glass surface">
      <div class="h-16 px-5 flex items-center gap-3">
        <a href="@auth {{ route('admin.dashboard') }} @else {{ route('posts.public') }} @endauth" class="flex items-center gap-2">
          <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 14.93V14h-2v2.93A8 8 0 014.07 13H6v-2H4.07A8 8 0 0111 7.07V10h2V7.07A8 8 0 0119.93 11H18v2h1.93A8 8 0 0113 16.93Z"/>
          </svg>
          <span class="text-lg font-extrabold tracking-tight">{{ config('app.name','Posts') }}</span>
        </a>
      </div>

      @auth
        <nav class="mt-3 px-3 space-y-1 text-sm">
          <a href="{{ route('admin.dashboard') }}"
             class="nav-item @if(request()->routeIs('admin.dashboard')) nav-item-active @endif border border-transparent">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9-1.5 1.5L12 5l-7.5 8.5L3 12zm2 8v-6h5v6H5zm7 0v-9h5v9h-5z"/></svg>
            <span class="font-semibold">Dashboard</span>
          </a>
          <a href="/" class="nav-item"> <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z"/></svg> Public Posts</a>
        </nav>

        <div class="mt-auto p-4 border-t border-slate-200/70 dark:border-slate-800/70">
          <div class="flex items-center justify-between">
            <button data-theme-toggle class="btn-ghost text-xs">Toggle Dark</button>
            <form method="POST" action="{{ route('logout') }}"> @csrf
              <button class="btn btn-primary text-xs">Logout</button>
            </form>
          </div>
        </div>
      @endauth

      @guest
        <nav class="mt-4 px-3">
          <a href="{{ route('posts.public') }}" class="nav-item nav-item-active border">Public Posts</a>
        </nav>
        <div class="mt-auto p-4 border-t border-slate-200/70 dark:border-slate-800/70">
          <div class="flex items-center justify-between">
            <button data-theme-toggle class="btn-ghost text-xs">Toggle Dark</button>
            <a href="{{ route('login') }}" class="btn btn-primary text-xs">Login</a>
          </div>
        </div>
      @endguest
    </aside>

    <!-- Mobile drawer (unchanged structure, benefits from new CSS) -->
    <aside id="drawer" class="fixed z-40 inset-y-0 left-0 w-72 surface transform -translate-x-full transition-transform duration-300 lg:hidden">
      <!-- … keep your mobile drawer contents … -->
      {{-- keep same inner markup as you have; styles are handled by CSS classes --}}
    </aside>

    <!-- Main -->
    <div class="flex-1 min-w-0 flex flex-col lg:ml-68">
      <header class="sticky top-0 z-20 glass surface-strong border-b border-slate-200/70 dark:border-slate-800/70">
        <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button id="open-drawer" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/></svg>
            </button>
            <div class="hidden md:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
              <span class="font-bold text-slate-800 dark:text-slate-100">@yield('page_title','Dashboard')</span>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <form action="#" onsubmit="return false;" class="hidden md:block">
              <div class="relative">
                <input class="input w-72 pl-10" placeholder="Search…"/>
                
              </div>
            </form>

            <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 relative">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2 2 0 002-2H10a2 2 0 002 2zm6-6V11a6 6 0 10-12 0v5l-2 2v1h16v-1l-2-2z"/></svg>
              <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-rose-500 rounded-full"></span>
            </button>

            @auth
              <div class="relative">
                <button id="user-menu-btn" class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                  <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 ring-2 ring-white/60 dark:ring-slate-900/60"></div>
                  <span class="hidden sm:block text-sm font-semibold">{{ auth()->user()->username ?? auth()->user()->email }}</span>
                  <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                </button>
                <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 rounded-xl surface shadow-lg overflow-hidden">
                  <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Profile</a>
                  <a href="#" class="block px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">Settings</a>
                  <div class="border-t border-slate-200/70 dark:border-slate-800/70"></div>
                  <form method="POST" action="{{ route('logout') }}"> @csrf
                    <button class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20">Logout</button>
                  </form>
                </div>
              </div>
            @endauth

            @guest <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-600 hover:underline">Login</a> @endguest
          </div>
        </div>
      </header>

      @if(session('message'))
        <div id="toast"
             class="toast opacity-100 pointer-events-auto">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4 1.5 1.5L11 17l-4.5-4.5L8 11l1 1z"/></svg>
          <div id="toastText" class="text-sm font-semibold">{{ session('message') }}</div>
          <button class="ml-3 opacity-80 hover:opacity-100" onclick="document.getElementById('toast').remove()">✕</button>
        </div>
      @endif

      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
          <h1 class="display-title">@yield('page_title','Dashboard')</h1>
          <div class="mt-3">@yield('page_actions')</div>
        </div>
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    // Close user menu on outside click
    (function(){
      const btn = document.getElementById('user-menu-btn');
      const menu = document.getElementById('user-menu');
      document.addEventListener('click', (e)=>{
        if(!menu || !btn) return;
        if(!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.add('hidden');
      });
      btn?.addEventListener('click', ()=> menu?.classList.toggle('hidden'));
    })();
  </script>
</body>
</html>
