@extends('layouts.guest')

@section('title', 'Login - E-Learning')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-[#0F0B2E]">

    {{-- ═══════════ BACKGROUND ANIMATED — gradient bergerak + orbs mengambang ═══════════ --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#1E1B4B] via-[#312E81] to-[#0F0B2E] bg-[length:200%_200%] animate-gradient-shift"></div>

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-[420px] h-[420px] rounded-full bg-[#4F46E5] opacity-20 blur-3xl -top-32 -left-32 animate-float-slow"></div>
        <div class="absolute w-[360px] h-[360px] rounded-full bg-[#818CF8] opacity-20 blur-3xl top-1/2 -right-24 animate-float-medium"></div>
        <div class="absolute w-[300px] h-[300px] rounded-full bg-[#7C3AED] opacity-20 blur-3xl bottom-0 left-1/4 animate-float-fast"></div>

        {{-- Partikel titik kecil mengambang --}}
        @for ($i = 0; $i < 18; $i++)
            <span class="absolute rounded-full bg-white/40 animate-particle"
                  style="
                      width: {{ rand(2,4) }}px; height: {{ rand(2,4) }}px;
                      left: {{ rand(2, 98) }}%; top: {{ rand(2, 98) }}%;
                      animation-duration: {{ rand(8, 18) }}s;
                      animation-delay: -{{ rand(0, 10) }}s;
                  "></span>
        @endfor
    </div>

    {{-- ═══════════ CARD LOGIN — glassmorphism ═══════════ --}}
    <div class="relative w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 rounded-[28px] overflow-hidden shadow-2xl shadow-indigo-950/50 rise-in"
         style="animation-delay:.05s">

        {{-- ═══════════ SISI KIRI — Hero branding ═══════════ --}}
        <div class="hidden md:flex relative flex-col justify-between p-10 bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] text-white overflow-hidden">
            <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08] animate-float-slow"></div>
            <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2 animate-float-medium"></div>
            <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07] animate-float-fast"></div>

            <div class="relative rise-in" style="animation-delay:.15s">
                {{-- Logo besar + ring glow hidup --}}
                <div class="relative w-24 h-24 mb-6">
                    <span class="absolute inset-0 rounded-3xl bg-white/20 animate-ping-slow"></span>
                    <div class="relative w-24 h-24 rounded-3xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center overflow-hidden shadow-lg shadow-black/20">
                        @if($schoolSetting?->logo_url)
                            <img src="{{ $schoolSetting->logo_url }}" alt="Logo {{ $schoolSetting->school_name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s4.332.477 5.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                </div>

                <h1 class="text-3xl lg:text-4xl font-extrabold leading-tight">{{ $schoolSetting->school_name ?? 'E-Learning' }}</h1>
                <p class="text-indigo-100 mt-3 text-sm leading-relaxed max-w-xs">Belajar, berdiskusi, dan berkembang bersama kelasmu kapan saja, di mana saja.</p>
            </div>

            <div class="relative space-y-3">
                @foreach(['Materi & kuis interaktif','Diskusi langsung dengan guru','Pantau progres belajarmu'] as $i => $feature)
                <div class="flex items-center gap-2.5 text-sm text-indigo-100 rise-in" style="animation-delay:{{ .3 + $i * .08 }}s">
                    <span class="w-5 h-5 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    {{ $feature }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- ═══════════ SISI KANAN — Form login ═══════════ --}}
        <div class="bg-white/95 backdrop-blur-xl p-6 sm:p-8 md:p-10 flex flex-col justify-center">

            {{-- Logo mobile only — juga diperbesar --}}
            <div class="text-center mb-6 md:hidden rise-in">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <span class="absolute inset-0 rounded-3xl bg-indigo-400/30 animate-ping-slow"></span>
                    <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center overflow-hidden shadow-lg shadow-indigo-300">
                        @if($schoolSetting?->logo_url)
                            <img src="{{ $schoolSetting->logo_url }}" alt="Logo {{ $schoolSetting->school_name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s4.332.477 5.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $schoolSetting->school_name ?? 'E-Learning' }}</h1>
            </div>

            <div class="mb-6 rise-in" style="animation-delay:.1s">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Selamat datang kembali 👋</h2>
                <p class="text-gray-400 mt-1 text-sm">Masuk ke akun kamu untuk lanjut belajar</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-gradient-to-r from-emerald-50 to-white border border-emerald-100 text-emerald-700 rounded-xl text-sm font-medium flex items-center gap-2 rise-in">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-gradient-to-r from-red-50 to-white border border-red-100 text-[#DC2626] rounded-xl text-sm font-medium flex items-center gap-2 rise-in">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                {{-- Email --}}
                <div class="mb-4 rise-in" style="animation-delay:.15s">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <div class="relative group">
                        <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-indigo-200 to-violet-200 opacity-0 group-focus-within:opacity-60 blur transition-opacity duration-300"></div>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-300 transition-colors group-focus-within:text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <input
                                type="email" id="email" name="email" value="{{ old('email') }}"
                                required autofocus
                                class="w-full pl-10 pr-4 py-3 border-2 rounded-xl text-sm font-medium focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 transition-all duration-200
                                       {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200' }}"
                                placeholder="nama@sekolah.sch.id"
                            >
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-4 rise-in" style="animation-delay:.2s" x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative group">
                        <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-indigo-200 to-violet-200 opacity-0 group-focus-within:opacity-60 blur transition-opacity duration-300"></div>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-300 transition-colors group-focus-within:text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <input
                                :type="show ? 'text' : 'password'" id="password" name="password" required
                                class="w-full pl-10 pr-24 py-3 border-2 rounded-xl text-sm font-medium focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 transition-all duration-200
                                       {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200' }}"
                                placeholder="Minimal 8 karakter"
                            >
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#4F46E5] hover:text-[#4338CA] text-xs font-semibold px-2 py-1 rounded-lg hover:bg-indigo-50 transition-colors">
                                <span x-text="show ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between mb-6 rise-in" style="animation-delay:.25s">
                    <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" id="remember" name="remember"
                               class="w-4 h-4 rounded border-gray-300 text-[#4F46E5] focus:ring-2 focus:ring-indigo-200 cursor-pointer">
                        <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Ingat saya</span>
                    </label>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" :disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 active:scale-[0.98] disabled:opacity-70 disabled:hover:translate-y-0 text-white font-bold py-3 rounded-xl text-sm transition-all duration-200 rise-in"
                    style="animation-delay:.3s">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Masuk'"></span>
                    <svg x-show="!loading" class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <p class="text-center text-xs md:text-sm text-gray-400 mt-6 rise-in" style="animation-delay:.35s">
                Siswa baru?
                <a href="{{ route('register') }}" class="text-[#4F46E5] hover:text-[#4338CA] hover:underline font-semibold">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .6s cubic-bezier(.16,1,.3,1) both; }

@keyframes gradient-shift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
.animate-gradient-shift { animation: gradient-shift 14s ease infinite; }

@keyframes float-slow {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(20px, -30px) scale(1.08); }
}
@keyframes float-medium {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(-25px, 20px) scale(1.05); }
}
@keyframes float-fast {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50%      { transform: translate(15px, 15px) scale(0.95); }
}
.animate-float-slow   { animation: float-slow 9s ease-in-out infinite; }
.animate-float-medium { animation: float-medium 7s ease-in-out infinite; }
.animate-float-fast   { animation: float-fast 6s ease-in-out infinite; }

@keyframes ping-slow {
    0%   { transform: scale(1);   opacity: .6; }
    75%, 100% { transform: scale(1.35); opacity: 0; }
}
.animate-ping-slow { animation: ping-slow 2.6s cubic-bezier(0,0,0.2,1) infinite; }

@keyframes particle-float {
    0%   { transform: translateY(0) translateX(0); opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 1; }
    100% { transform: translateY(-120px) translateX(20px); opacity: 0; }
}
.animate-particle { animation: particle-float linear infinite; }

[x-cloak] { display: none !important; }
</style>
@endsection