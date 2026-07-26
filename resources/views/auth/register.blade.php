@extends('layouts.guest')

@section('title', 'Daftar Akun - E-Learning')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-[#0F0B2E]">

    {{-- ═══════════ BACKGROUND ANIMATED — sama dengan login ═══════════ --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#1E1B4B] via-[#312E81] to-[#0F0B2E] bg-[length:200%_200%] animate-gradient-shift"></div>

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-[420px] h-[420px] rounded-full bg-[#4F46E5] opacity-20 blur-3xl -top-32 -left-32 animate-float-slow"></div>
        <div class="absolute w-[360px] h-[360px] rounded-full bg-[#818CF8] opacity-20 blur-3xl top-1/2 -right-24 animate-float-medium"></div>
        <div class="absolute w-[300px] h-[300px] rounded-full bg-[#7C3AED] opacity-20 blur-3xl bottom-0 left-1/4 animate-float-fast"></div>

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

    {{-- ═══════════ CARD REGISTER — glassmorphism, konsisten dengan login ═══════════ --}}
    <div class="relative w-full max-w-5xl bg-white/95 backdrop-blur-xl rounded-[28px] shadow-2xl shadow-indigo-950/50 border border-white/10 overflow-hidden flex flex-col md:flex-row my-auto rise-in"
         style="animation-delay:.05s">

        {{-- ═══════════ SISI KIRI — Hero branding ═══════════ --}}
        <div class="md:w-5/12 relative flex flex-col justify-between p-8 lg:p-10 bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] text-white overflow-hidden flex-shrink-0">
            <div class="absolute -top-12 -left-12 w-48 h-48 rounded-full bg-white opacity-[0.08] animate-float-slow"></div>
            <div class="absolute top-1/2 -right-16 w-56 h-56 rounded-full bg-white opacity-[0.06] animate-float-medium"></div>
            <div class="absolute -bottom-12 -left-8 w-40 h-40 rounded-full bg-white opacity-[0.07] animate-float-fast"></div>

            <div class="relative z-10 rise-in" style="animation-delay:.15s">
                {{-- Logo besar + ring glow hidup, samain kayak login --}}
                <div class="relative w-20 h-20 lg:w-24 lg:h-24 mb-6">
                    <span class="absolute inset-0 rounded-3xl bg-white/20 animate-ping-slow"></span>
                    <div class="relative w-20 h-20 lg:w-24 lg:h-24 rounded-3xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center overflow-hidden shadow-lg shadow-black/20">
                        @if($schoolSetting?->logo_url)
                            <img src="{{ $schoolSetting->logo_url }}" alt="Logo {{ $schoolSetting->school_name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-9 h-9 lg:w-11 lg:h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                </div>

                <h2 class="text-2xl lg:text-3xl font-bold tracking-tight mb-2">Mulai Belajar!</h2>
                <p class="text-indigo-100 text-xs lg:text-sm leading-relaxed mb-8">
                    Daftarkan akunmu dan akses materi, tugas, serta kuis kapan saja.
                </p>
            </div>

            {{-- Feature List --}}
            <div class="relative z-10 space-y-3 pt-4 border-t border-white/10 text-xs text-indigo-50">
                @foreach([
                    'Akses materi & kuis interaktif',
                    'Kumpulkan tugas langsung via aplikasi',
                    'Diskusi langsung dengan guru',
                    'Pantau progres & nilai real-time'
                ] as $i => $feature)
                <div class="flex items-center gap-2.5 rise-in" style="animation-delay:{{ .3 + $i * .08 }}s">
                    <span class="w-5 h-5 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span>{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ═══════════ SISI KANAN — Form Register ═══════════ --}}
        <div class="md:w-7/12 p-6 sm:p-8 lg:p-10 flex flex-col justify-center">

            <div class="mb-6 rise-in" style="animation-delay:.1s">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-indigo-600 mb-3 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Login
                </a>

                <h1 class="text-xl lg:text-2xl font-bold text-slate-800 tracking-tight">Buat Akun Baru 👋</h1>
                <p class="text-xs text-slate-400 mt-1">Khusus untuk siswa yang terdaftar aktif di sekolah.</p>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
            <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-2xl text-xs text-red-600 rise-in">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}"
                  x-data="{ showPass: false, showConfirm: false, loading: false }"
                  @submit="loading = true"
                  class="space-y-3.5">
                @csrf

                {{-- Grid untuk NISN & Tanggal Lahir --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rise-in" style="animation-delay:.15s">
                    {{-- NISN --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">NISN <span class="text-red-500">*</span></label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" maxlength="10" inputmode="numeric" required
                            placeholder="10 digit NISN"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    </div>
                </div>

                {{-- Nama Lengkap --}}
                <div class="rise-in" style="animation-delay:.18s">
                    <label class="block text-xs font-medium text-slate-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                        <span class="text-[10px] text-slate-400 font-normal">(sesuai sekolah)</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Nama lengkap kamu"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                {{-- Email --}}
                <div class="rise-in" style="animation-delay:.21s">
                    <label class="block text-xs font-medium text-slate-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="email@kamu.com"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                {{-- Password & Konfirmasi Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rise-in" style="animation-delay:.24s">
                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                placeholder="Min. 8 karakter"
                                class="w-full px-3.5 py-2.5 pr-16 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition select-none">
                                <span x-text="showPass ? 'Tutup' : 'Lihat'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Ulangi password"
                                class="w-full px-3.5 py-2.5 pr-16 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition select-none">
                                <span x-text="showConfirm ? 'Tutup' : 'Lihat'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="p-3 bg-indigo-50/60 border border-indigo-100 rounded-xl text-[11px] text-indigo-900 flex gap-2 items-start rise-in" style="animation-delay:.27s">
                    <svg class="w-4 h-4 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Setelah mendaftar, kami akan mengirimkan email verifikasi ke akunmu.</span>
                </div>

                {{-- Submit Button --}}
                <button type="submit" :disabled="loading"
                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 active:scale-[0.98] disabled:opacity-70 disabled:hover:translate-y-0 text-white font-semibold py-2.5 rounded-xl text-xs transition-all duration-200 mt-2 rise-in"
                    style="animation-delay:.3s">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Daftar Sekarang'"></span>
                    <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>

                {{-- Link ke Login --}}
                <p class="text-center text-xs text-slate-500 pt-2 rise-in" style="animation-delay:.33s">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Masuk di sini</a>
                </p>
            </form>
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