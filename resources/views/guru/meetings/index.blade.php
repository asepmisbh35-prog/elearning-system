{{-- resources/views/guru/meetings/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pertemuan — ' . $class->name)
@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 md:py-8" x-data="{ confirmDelete: false, deleteForm: null }">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative">
            <a href="{{ route('guru.classes.show', $class) }}"
               class="inline-flex items-center gap-1 text-sm text-indigo-100 hover:text-white transition group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke kelas
            </a>
            <h1 class="text-xl md:text-2xl font-bold mt-2">Pertemuan</h1>
            <p class="text-indigo-100 text-sm md:text-base">{{ $class->name }}</p>

            <div class="mt-4 flex items-center justify-between gap-3">
                <div class="bg-white/10 border border-white/15 rounded-lg px-2.5 py-1 text-xs font-semibold">
                    {{ $meetings->count() }} pertemuan
                </div>
                <a href="{{ route('guru.classes.meetings.create', $class) }}"
                   class="inline-flex items-center gap-1.5 bg-white text-[#4338CA] px-4 py-2.5 rounded-xl font-medium text-sm shadow-lg hover:-translate-y-0.5 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pertemuan
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm rise-in flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-[#10B981]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Timeline Pertemuan --}}
    @forelse ($meetings as $i => $meeting)
        <div class="relative pl-14 md:pl-16 pb-6 rise-in" style="animation-delay: {{ $i * 70 }}ms"
             x-data="{ open: false }">

            {{-- garis penghubung --}}
            @if (!$loop->last)
                <span class="absolute left-[22px] md:left-[26px] top-11 bottom-0 w-0.5 bg-indigo-100"></span>
            @endif

            {{-- badge nomor --}}
            <div class="absolute left-0 top-0 w-11 h-11 md:w-[52px] md:h-[52px] rounded-full flex items-center justify-center font-bold text-sm md:text-base leading-none shadow-md
                        {{ $meeting->status === 'published' ? 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8] text-white shadow-indigo-200/60' : 'bg-indigo-50 text-[#4F46E5] border-2 border-indigo-100' }}">
                {{ $meeting->order }}
            </div>

            {{-- card konten --}}
            <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-4 md:p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-200/60 transition-all duration-200">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm md:text-base">
                            {{ $meeting->topic }}
                        </p>
                        <p class="text-xs md:text-sm text-gray-500 mt-1 flex items-center gap-1.5 flex-wrap">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            {{ $meeting->scheduled_at?->translatedFormat('d M Y, H:i') ?? 'Belum dijadwalkan' }}
                            <span class="text-gray-300">&middot;</span>
                            {{ $meeting->materials_count }} materi
                            <span class="text-gray-300">&middot;</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                {{ $meeting->status === 'published' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $meeting->status === 'published' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $meeting->status === 'published' ? 'Published' : 'Draft' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-4 text-xs md:text-sm font-medium flex-wrap">
                    <a href="{{ route('guru.classes.meetings.attendance.show', [$class, $meeting]) }}"
                       class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 group">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Absensi
                    </a>
                    <a href="{{ route('guru.meetings.materials.index', $meeting) }}"
                       class="inline-flex items-center gap-1 text-[#4F46E5] hover:text-[#4338CA] group">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                        Materi
                    </a>
                    <a href="{{ route('guru.classes.meetings.edit', [$class, $meeting]) }}"
                       class="inline-flex items-center gap-1 text-gray-600 hover:text-gray-800 group">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                        </svg>
                        Edit
                    </a>

                    <form action="{{ route('guru.classes.meetings.destroy', [$class, $meeting]) }}" method="POST" x-ref="deleteForm{{ $meeting->id }}">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button"
                            @click="confirmDelete = true; deleteForm = $refs['deleteForm{{ $meeting->id }}']"
                            class="inline-flex items-center gap-1 text-[#EF4444] hover:text-red-700 group ml-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-10 text-center rise-in">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
            <p class="text-sm text-gray-500">
                Belum ada pertemuan. Klik "Tambah Pertemuan" untuk membuat pertemuan ke-1.
            </p>
        </div>
    @endforelse

    {{-- Modal Konfirmasi Hapus (custom, satu untuk semua baris) --}}
    <div x-show="confirmDelete" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl md:rounded-3xl p-6 max-w-sm w-full shadow-xl"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
            <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1.5">Hapus Pertemuan?</h3>
            <p class="text-sm text-gray-500 mb-5">Materi di dalam pertemuan ini juga akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex gap-3">
                <button @click="confirmDelete = false" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">Batal</button>
                <button @click="deleteForm.submit()" class="flex-1 px-4 py-2.5 bg-[#EF4444] hover:bg-red-600 text-white rounded-xl text-sm font-medium">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
[x-cloak] { display: none !important; }
</style>
@endsection