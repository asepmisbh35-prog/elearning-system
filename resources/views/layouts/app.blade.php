<!DOCTYPE html>
<html lang="id"
      x-data="{
          sidebarOpen: window.innerWidth >= 1024,
          mobileOpen: false,
          isMobile: window.innerWidth < 768,
          isTablet: window.innerWidth >= 768 && window.innerWidth < 1024
      }"
      x-init="
          window.addEventListener('resize', () => {
              isMobile = window.innerWidth < 768;
              isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
              if (window.innerWidth >= 1024) { sidebarOpen = true; mobileOpen = false; }
              else if (window.innerWidth >= 768) { sidebarOpen = false; mobileOpen = false; }
              else { sidebarOpen = false; mobileOpen = false; }
          });
          if (isTablet) { sidebarOpen = false; }
      ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Learning') — {{ $schoolSetting->school_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
</head>
<body class="bg-[#F8FAFC] font-sans antialiased" style="font-family: 'Plus Jakarta Sans', Inter, sans-serif;">

@auth
@php
    $role = auth()->user()->role;

    $isDarkSidebar = $role === 'admin';

    $sidebarBorder = 'border-white/10';
    $textDefault   = 'text-indigo-100';
    $textMuted     = 'text-indigo-300';
    $hoverBg       = 'hover:bg-white/10';
    $activeBg      = 'bg-white/15';
    $activeText    = 'text-white';
    $brandText     = 'text-white';

    $menus = [
        'admin' => [
            ['route' => 'admin.dashboard',           'label' => 'Dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'admin.users.index',         'label' => 'Pengguna',       'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'admin.kkm.index',           'label' => 'KKM Sekolah',    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'admin.announcements.index', 'label' => 'Pengumuman',     'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'badge_count' => $announcementUnreadCount ?? 0],
            ['route' => 'admin.settings.edit',       'label' => 'Identitas Sekolah', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ],
        'guru' => [
            ['route' => 'guru.dashboard',               'label' => 'Dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'guru.classes.index',           'label' => 'Kelas Saya', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['route' => 'guru.quizzes.questions.index', 'label' => 'Bank Soal',  'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'guru.announcements.index',     'label' => 'Pengumuman', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
            ['route' => 'guru.messages.index',          'label' => 'Pesan',      'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'badge_count' => $unreadMessageCount ?? 0],
            ['route' => 'guru.calendar.index',          'label' => 'Kalender',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ],
        'siswa' => [
            ['route' => 'siswa.dashboard',      'label' => 'Dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'siswa.classes.index',  'label' => 'Kelas Saya', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['route' => 'siswa.messages.index', 'label' => 'Pesan',      'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'badge_count' => $unreadMessageCount ?? 0],
            ['route' => 'siswa.calendar.index', 'label' => 'Kalender',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ],
    ];

    $bottomNavSiswa = [
        ['route' => 'siswa.dashboard',      'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'siswa.classes.index',  'label' => 'Kelas',     'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['route' => 'siswa.messages.index', 'label' => 'Pesan',     'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'badge_count' => $unreadMessageCount ?? 0],
        ['route' => 'profile.show',         'label' => 'Profil',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];

    $roleLabel = ['admin' => 'Administrator', 'guru' => 'Guru', 'siswa' => 'Siswa'];

    $avatarUrl = auth()->user()->photo
        ? Storage::url(auth()->user()->photo)
        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=4F46E5&color=fff&size=80';
@endphp

<div class="flex h-screen overflow-hidden relative">

    {{-- ══════════════ BACKDROP (mobile drawer, Guru & Admin) ══════════════ --}}
    @if($role !== 'siswa')
    <div x-show="mobileOpen"
         x-transition.opacity
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/40 z-30 md:hidden"
         style="display: none;"></div>
    @endif

    {{-- ══════════════ SIDEBAR — gradient Indigo + motif lingkaran ══════════════ --}}
    <aside
        class="border-r {{ $sidebarBorder }} flex flex-col transition-all duration-200 ease-in-out z-40
               fixed md:relative inset-y-0 left-0 overflow-hidden
               {{ $isDarkSidebar ? 'bg-gradient-to-b from-[#1E1B4B] via-[#0F172A] to-[#0F172A]' : 'bg-gradient-to-b from-[#4F46E5] via-[#4338CA] to-[#3730A3]' }}
               {{ $role === 'siswa' ? 'hidden md:flex' : '' }}"
        :class="{
            'w-[280px]': (isMobile && mobileOpen) || (!isMobile && sidebarOpen),
            'w-[72px]': !isMobile && !sidebarOpen,
            '-translate-x-full md:translate-x-0': isMobile && !mobileOpen,
            'translate-x-0': !isMobile || mobileOpen
        }">

        {{-- ══ Dekorasi: lingkaran besar transparan, senada hero card dashboard ══ --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-16 w-64 h-64 rounded-full bg-white opacity-[0.08]"></div>
            <div class="absolute top-1/3 -left-20 w-56 h-56 rounded-full bg-white opacity-[0.06]"></div>
            <div class="absolute bottom-24 -right-10 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>
            <div class="absolute bottom-0 left-1/4 w-72 h-72 rounded-full bg-white opacity-[0.05] translate-y-1/2"></div>
        </div>

        {{-- Brand --}}
        <div class="relative flex items-center gap-3 px-4 h-16 border-b {{ $sidebarBorder }} whitespace-nowrap flex-shrink-0">
            <div class="w-8 h-8 bg-white/20 backdrop-blur rounded-[10px] flex items-center justify-center flex-shrink-0 shadow-lg overflow-hidden">
                @if($schoolSetting?->logo_url)
                    <img src="{{ $schoolSetting->logo_url }}" alt="Logo {{ $schoolSetting->school_name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                @endif
            </div>
            <span class="font-bold text-sm leading-tight {{ $brandText }}" x-show="isMobile ? mobileOpen : sidebarOpen" x-transition>
                {{ $schoolSetting->school_name ?? 'E-Learning' }}<br>
                <span class="{{ $textMuted }} font-normal text-xs">Portal {{ $roleLabel[$role] ?? '' }}</span>
            </span>
            <button @click="mobileOpen = false" class="ml-auto md:hidden {{ $textMuted }} hover:{{ $brandText }}" x-show="mobileOpen">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Menu Utama --}}
        <nav class="relative flex-1 py-4 px-2 space-y-1 overflow-y-auto whitespace-nowrap">
            @foreach ($menus[$role] ?? [] as $menu)
                @continue(!Route::has($menu['route']))
                @php $isActive = request()->routeIs($menu['route']); @endphp
                <a href="{{ route($menu['route']) }}"
                   @click="mobileOpen = false"
                   title="{{ $menu['label'] }}"
                   class="relative flex items-center gap-3 px-3 py-2.5 rounded-[12px] text-sm transition
                          {{ $isActive ? $activeBg.' '.$activeText.' font-semibold shadow-sm' : $textDefault.' '.$hoverBg }}">
                    @if($isActive)
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 bg-white rounded-r"></span>
                    @endif

                    <span class="relative flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/>
                        </svg>
                        @if(($menu['badge_count'] ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-[#EF4444] rounded-full border-2 border-[#4338CA]"></span>
                        @endif
                    </span>

                    <span x-show="isMobile ? mobileOpen : sidebarOpen" x-transition class="truncate">{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User Card — sticky bottom --}}
        <div class="relative border-t {{ $sidebarBorder }} p-3 whitespace-nowrap flex-shrink-0">

            <div x-show="isMobile ? mobileOpen : sidebarOpen" x-transition>
                <a href="{{ route('profile.show') }}"
                   @click="mobileOpen = false"
                   class="flex items-center gap-3 px-2 py-2 rounded-[12px] transition {{ $hoverBg }}
                          {{ request()->routeIs('profile.*') ? $activeBg : '' }}">
                    <img src="{{ $avatarUrl }}"
                         alt="Foto {{ auth()->user()->name }}"
                         class="w-9 h-9 rounded-full object-cover flex-shrink-0 border-2 border-white/30">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $brandText }} truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs {{ $textMuted }}">{{ $roleLabel[$role] ?? '' }}</p>
                    </div>
                    <svg class="w-4 h-4 {{ $textMuted }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit"
                            class="w-full text-left text-xs {{ $textMuted }} hover:{{ $brandText }} transition px-2 py-1.5 rounded-[12px] {{ $hoverBg }}">
                        Keluar →
                    </button>
                </form>
            </div>

            <div x-show="!isMobile && !sidebarOpen" x-transition class="flex justify-center">
                <a href="{{ route('profile.show') }}" title="{{ auth()->user()->name }} — Profil">
                    <img src="{{ $avatarUrl }}"
                         alt="Foto {{ auth()->user()->name }}"
                         class="w-8 h-8 rounded-full object-cover border-2 border-white/30 hover:border-white transition">
                </a>
            </div>
        </div>
    </aside>

    {{-- ══════════════ MAIN CONTENT ══════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden w-full">

        {{-- Navbar atas --}}
        <header class="bg-white border-b border-[#E2E8F0] px-4 md:px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3 md:gap-4">
                <button @click="isMobile ? (mobileOpen = !mobileOpen) : (sidebarOpen = !sidebarOpen)"
                        class="text-[#94A3B8] hover:text-[#475569] transition"
                        x-show="{{ $role === 'siswa' ? '!isMobile' : 'true' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base md:text-lg font-bold text-[#0F172A] truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3 md:gap-4 flex-shrink-0">
                <x-notification-badge />

                <a href="{{ route('profile.show') }}" title="Profil Saya" class="flex-shrink-0">
                    <img src="{{ $avatarUrl }}"
                        alt="Foto {{ auth()->user()->name }}"
                        class="w-8 h-8 rounded-full object-cover border-2 border-[#E2E8F0] hover:border-[#4F46E5] transition">
                </a>
            </div>
        </header>

        {{-- Area konten --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6 {{ $role === 'siswa' ? 'pb-24 md:pb-6' : '' }}">
            @if(session('success'))
                <div class="mb-4 p-4 bg-[#10B981]/10 border border-[#10B981]/30 text-[#065F46] text-sm rounded-[12px] flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-[#EF4444]/10 border border-[#EF4444]/30 text-[#7F1D1D] text-sm rounded-[12px]">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- ══════════════ BOTTOM NAVIGATION (khusus Siswa, mobile only) — floating pill style ══════════════ --}}
    @if($role === 'siswa')
    <nav class="md:hidden fixed bottom-3 left-3 right-3 z-40">
        <div class="bg-white/90 backdrop-blur-lg border border-[#E2E8F0] rounded-[24px] shadow-xl shadow-indigo-900/10 h-[68px] flex items-stretch px-2 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-[#EEF2FF] via-white to-[#EEF2FF] opacity-60 pointer-events-none"></div>

            @foreach ($bottomNavSiswa as $menu)
                @continue(!Route::has($menu['route']))
                @php $isActive = request()->routeIs($menu['route']); @endphp
                <a href="{{ route($menu['route']) }}"
                   class="relative flex-1 flex flex-col items-center justify-center gap-1 z-10">
                    <span class="absolute inset-x-2 top-1.5 bottom-1.5 rounded-2xl transition-all duration-200
                                 {{ $isActive ? 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8] shadow-lg shadow-indigo-500/40 scale-100' : 'scale-90 opacity-0' }}"></span>

                    <span class="relative flex flex-col items-center gap-0.5 py-1">
                        <span class="relative">
                            <svg class="w-6 h-6 transition-colors {{ $isActive ? 'text-white' : 'text-[#94A3B8]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/>
                            </svg>
                            @if(($menu['badge_count'] ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-[#EF4444] rounded-full border-2 border-white"></span>
                            @endif
                        </span>
                        <span class="text-[10px] font-semibold transition-colors {{ $isActive ? 'text-white' : 'text-[#94A3B8]' }}">
                            {{ $menu['label'] }}
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </nav>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook/dflip/js/dflip.min.js"></script>
<script>
    DFLIP.defaults.scrollWheel = false;
</script>
@stack('scripts')

@else
<div class="min-h-screen bg-[#F8FAFC]">
    @yield('content')
</div>
@endauth

</body>
</html>