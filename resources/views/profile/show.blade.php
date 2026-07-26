@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 md:py-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-gradient-to-r from-emerald-50 to-white border border-emerald-100 text-emerald-700 text-sm font-medium rounded-xl px-4 py-3 mb-5">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-4 md:gap-5 items-start">

        {{-- ═══════════ KOLOM KIRI ═══════════ --}}
        <div class="flex flex-col gap-4 md:gap-5">

            {{-- Card Avatar --}}
            <div class="relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 text-center shadow-sm shadow-indigo-100/40 overflow-hidden">

                {{-- Aksen dekoratif atas, senada hero card --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 opacity-60 blur-xl"></div>

                <div class="relative">
                    {{-- Avatar --}}
                    <div class="relative w-24 h-24 md:w-28 md:h-28 mx-auto mb-3.5">
                        <div class="absolute -inset-1 rounded-full bg-gradient-to-br from-[#4F46E5] to-[#818CF8] opacity-80"></div>
                        <img id="sidebar-avatar"
                             src="{{ $user->photo ? Storage::url($user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4F46E5&color=fff&size=192' }}"
                             alt="{{ $user->name }}"
                             class="relative w-24 h-24 md:w-28 md:h-28 rounded-full object-cover border-[3px] border-white">

                        <label for="photo-input"
                               class="absolute bottom-0 right-0 w-8 h-8 md:w-9 md:h-9 bg-gradient-to-br from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:scale-110 rounded-full flex items-center justify-center cursor-pointer border-2 border-white transition-all duration-200"
                               title="Ganti foto">
                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </label>
                    </div>

                    <p class="font-bold text-gray-900 text-base md:text-lg">{{ $user->name }}</p>
                    <p class="text-xs md:text-sm text-gray-400 mt-0.5 mb-3 break-all">{{ $user->email }}</p>

                    {{-- Role badge --}}
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-gradient-to-r from-[#1E1B4B] to-[#3730A3] text-white px-3.5 py-1.5 rounded-full shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Administrator
                        </span>
                    @elseif($user->role === 'guru')
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-gradient-to-r from-[#059669] to-[#34D399] text-white px-3.5 py-1.5 rounded-full shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            Guru
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-3.5 py-1.5 rounded-full shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>
                            Siswa
                        </span>
                    @endif

                    {{-- Form upload tersembunyi --}}
                    <form method="POST" action="{{ route('profile.photo.update') }}"
                          enctype="multipart/form-data" id="photo-form">
                        @csrf
                        <input type="file" id="photo-input" name="photo"
                               accept="image/jpeg,image/png"
                               class="hidden"
                               onchange="previewPhoto(this)">
                    </form>

                    {{-- Preview konfirmasi --}}
                    <div id="photo-preview-wrap" class="hidden mt-4 pt-4 border-t border-gray-100">
                        <img id="photo-preview" src="" alt="Preview"
                             class="w-16 h-16 rounded-full object-cover border-2 border-indigo-300 mx-auto mb-2.5 shadow-md shadow-indigo-100">
                        <p class="text-xs text-gray-500 mb-2.5 font-medium">Konfirmasi upload foto ini?</p>
                        <div class="flex gap-2 justify-center">
                            <button type="button"
                                    onclick="document.getElementById('photo-form').submit()"
                                    class="inline-flex items-center gap-1 text-xs font-bold bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 text-white px-3.5 py-1.5 rounded-xl transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Upload
                            </button>
                            <button type="button" onclick="cancelPreview()"
                                    class="text-xs font-bold border-2 border-red-200 text-[#EF4444] hover:bg-red-50 px-3.5 py-1.5 rounded-xl transition-all duration-200">
                                Batal
                            </button>
                        </div>
                    </div>

                    @error('photo')
                        <p class="text-xs text-[#EF4444] mt-2 font-medium">{{ $message }}</p>
                    @enderror

                    {{-- Hapus foto --}}
                    @if($user->photo)
                    <div class="mt-3.5 pt-3.5 border-t border-gray-100">
                        <form method="POST" action="{{ route('profile.photo.delete') }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Hapus foto profil?')"
                                    class="inline-flex items-center gap-1 text-xs text-[#EF4444] hover:text-[#DC2626] font-semibold hover:underline transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus foto
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Statistik Guru --}}
            @if($user->role === 'guru')
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3.5 pb-2.5 border-b border-gray-100 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Statistik Mengajar
                </p>
                <div class="grid grid-cols-3 gap-2 md:gap-2.5">
                    <div class="bg-gradient-to-br from-indigo-50 to-white border border-indigo-100 rounded-xl p-3 text-center">
                        <p class="text-xl md:text-2xl font-extrabold text-[#4338CA]">{{ $totalKelas }}</p>
                        <p class="text-[10px] md:text-xs text-indigo-400 mt-0.5 uppercase tracking-wide font-semibold">Kelas</p>
                    </div>
                    <div class="bg-gradient-to-br from-violet-50 to-white border border-violet-100 rounded-xl p-3 text-center">
                        <p class="text-xl md:text-2xl font-extrabold text-[#6D28D9]">{{ $totalSiswa }}</p>
                        <p class="text-[10px] md:text-xs text-violet-400 mt-0.5 uppercase tracking-wide font-semibold">Siswa</p>
                    </div>
                    <div class="bg-gradient-to-br from-sky-50 to-white border border-sky-100 rounded-xl p-3 text-center">
                        <p class="text-xl md:text-2xl font-extrabold text-[#0369A1]">{{ $totalMateri }}</p>
                        <p class="text-[10px] md:text-xs text-sky-400 mt-0.5 uppercase tracking-wide font-semibold">Materi</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Info Akademik Siswa --}}
            @if($user->role === 'siswa')
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3.5 pb-2.5 border-b border-gray-100 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    Informasi Akademik
                </p>
                @foreach([
                    ['NISN',          $student?->nisn ?? '-'],
                    ['Rombel',        $student?->rombel?->name ?? '-'],
                    ['Tanggal Lahir', $student?->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-'],
                    ['Jenis Kelamin', $student?->gender ?? '-'],
                ] as [$key, $val])
                <div class="flex justify-between items-center py-2.5 border-b border-gray-50 last:border-0 text-sm">
                    <span class="text-gray-400 font-medium">{{ $key }}</span>
                    <span class="font-semibold text-gray-800">{{ $val }}</span>
                </div>
                @endforeach
                <p class="text-xs text-gray-400 mt-3 bg-gray-50 rounded-lg px-3 py-2">Hubungi admin untuk mengubah data ini.</p>
            </div>
            @endif

        </div>{{-- akhir kolom kiri --}}


        {{-- ═══════════ KOLOM KANAN ═══════════ --}}
        <div class="flex flex-col gap-4 md:gap-5">

            {{-- Info Pribadi — Admin & Guru --}}
            @if(in_array($user->role, ['admin', 'guru']))
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 pb-2.5 border-b border-gray-100 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informasi Pribadi
                </p>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               placeholder="Masukkan nama lengkap"
                               class="w-full bg-gray-50 border-2 {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200' }} rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all duration-200">
                        @error('name')<p class="text-xs text-[#EF4444] mt-1.5 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <input type="email" value="{{ $user->email }}"
                                   class="w-full bg-gray-100 border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-400 cursor-not-allowed"
                                   disabled readonly>
                            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">Email tidak dapat diubah.</p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="phone">Nomor Telepon</label>
                        <input type="text" id="phone" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="Contoh: 08123456789"
                               class="w-full bg-gray-50 border-2 {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-200' }} rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all duration-200">
                        @error('phone')<p class="text-xs text-[#EF4444] mt-1.5 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
            @endif

            {{-- Akun Siswa read-only --}}
            @if($user->role === 'siswa')
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3.5 pb-2.5 border-b border-gray-100">Akun</p>
                <div class="flex justify-between items-center py-2.5 border-b border-gray-50 text-sm">
                    <span class="text-gray-400 font-medium">Nama</span>
                    <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2.5 text-sm">
                    <span class="text-gray-400 font-medium">Email</span>
                    <span class="font-semibold text-gray-800">{{ $user->email }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-3 bg-gray-50 rounded-lg px-3 py-2">Hubungi admin untuk mengubah nama atau email.</p>
            </div>
            @endif

            {{-- Ganti Password --}}
            <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-6 shadow-sm shadow-indigo-100/40">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 pb-2.5 border-b border-gray-100 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Keamanan Akun
                </p>
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf @method('PATCH')

                    {{-- Password lama --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="current_password">
                            Password Saat Ini
                        </label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password"
                                   placeholder="Masukkan password saat ini"
                                   autocomplete="current-password"
                                   class="w-full bg-gray-50 border-2 {{ $errors->has('current_password') ? 'border-red-300' : 'border-gray-200' }} rounded-xl pl-3.5 pr-11 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all duration-200">
                            <button type="button" onclick="togglePass('current_password', this)"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg hover:bg-indigo-50 flex items-center justify-center text-gray-400 hover:text-[#4F46E5] text-sm transition-colors">👁</button>
                        </div>
                        @error('current_password')<p class="text-xs text-[#EF4444] mt-1.5 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password baru --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="new_password">
                            Password Baru
                        </label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password"
                                   placeholder="Minimal 8 karakter"
                                   autocomplete="new-password"
                                   oninput="checkStrength(this.value)"
                                   class="w-full bg-gray-50 border-2 {{ $errors->has('new_password') ? 'border-red-300' : 'border-gray-200' }} rounded-xl pl-3.5 pr-11 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all duration-200">
                            <button type="button" onclick="togglePass('new_password', this)"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg hover:bg-indigo-50 flex items-center justify-center text-gray-400 hover:text-[#4F46E5] text-sm transition-colors">👁</button>
                        </div>
                        {{-- Strength bar — 4 segmen visual --}}
                        <div class="mt-2 flex gap-1">
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                                <div id="strength-seg-1" class="h-full rounded-full transition-all duration-300 bg-gray-100" style="width:0%"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                                <div id="strength-seg-2" class="h-full rounded-full transition-all duration-300 bg-gray-100" style="width:0%"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                                <div id="strength-seg-3" class="h-full rounded-full transition-all duration-300 bg-gray-100" style="width:0%"></div>
                            </div>
                            <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden">
                                <div id="strength-seg-4" class="h-full rounded-full transition-all duration-300 bg-gray-100" style="width:0%"></div>
                            </div>
                        </div>
                        {{-- Bar lama dipertahankan (hidden) supaya JS checkStrength tetap kompatibel tanpa perlu diubah --}}
                        <div class="hidden">
                            <div id="strength-bar"></div>
                        </div>
                        <p id="strength-lbl" class="text-xs mt-1.5 font-semibold text-gray-400"></p>
                        @error('new_password')<p class="text-xs text-[#EF4444] mt-1.5 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Konfirmasi --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="new_password_confirmation">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               placeholder="Ulangi password baru"
                               autocomplete="new-password"
                               class="w-full bg-gray-50 border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 focus:bg-white transition-all duration-200">
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Perbarui Password
                    </button>
                </form>
            </div>

        </div>{{-- akhir kolom kanan --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto maksimal 2MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('photo-preview').src = e.target.result;
        document.getElementById('sidebar-avatar').src = e.target.result;
        document.getElementById('photo-preview-wrap').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function cancelPreview() {
    const av = document.getElementById('sidebar-avatar');
    av.src = av.dataset.original;
    document.getElementById('photo-input').value = '';
    document.getElementById('photo-preview-wrap').classList.add('hidden');
}

window.addEventListener('DOMContentLoaded', () => {
    const av = document.getElementById('sidebar-avatar');
    if (av) av.dataset.original = av.src;
});

function togglePass(id, btn) {
    const el = document.getElementById(id);
    const hidden = el.type === 'password';
    el.type = hidden ? 'text' : 'password';
    btn.textContent = hidden ? '🙈' : '👁';
}

function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-lbl');
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const map = [
        { w: '0%',   bg: '',        txt: '' },
        { w: '25%',  bg: '#EF4444', txt: 'Lemah' },
        { w: '50%',  bg: '#F59E0B', txt: 'Sedang' },
        { w: '75%',  bg: '#6366F1', txt: 'Kuat' },
        { w: '100%', bg: '#10B981', txt: 'Sangat kuat' },
    ];
    const m = map[score] ?? map[0];

    // Bar lama (kompatibilitas, hidden)
    if (bar) { bar.style.width = m.w; bar.style.background = m.bg; }

    // 4 segmen visual baru
    for (let i = 1; i <= 4; i++) {
        const seg = document.getElementById('strength-seg-' + i);
        if (!seg) continue;
        if (i <= score) {
            seg.style.width = '100%';
            seg.style.background = m.bg;
        } else {
            seg.style.width = '0%';
            seg.style.background = '';
        }
    }

    lbl.textContent = m.txt;
    lbl.style.color = m.bg;
}
</script>
@endpush