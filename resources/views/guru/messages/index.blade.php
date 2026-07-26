{{-- resources/views/guru/messages/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Pesan')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 md:py-8"
     x-data="{ tab: 'all', search: '{{ request('search') }}' }">

    {{-- ============ HERO ============ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#4F46E5] via-[#4338CA] to-[#3730A3] p-5 md:p-8 text-white mb-6 rise-in">
        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-white opacity-[0.08]"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 rounded-full bg-white opacity-[0.06] translate-y-1/2"></div>
        <div class="absolute top-1/2 -left-16 w-40 h-40 rounded-full bg-white opacity-[0.07]"></div>

        <div class="relative flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-indigo-100 text-xs md:text-sm font-medium tracking-wide uppercase">Portal Guru</p>
                <h1 class="text-xl md:text-3xl font-bold mt-1">Pesan</h1>
                <p class="text-indigo-100 text-sm md:text-base mt-0.5">Percakapan dengan siswa & siaran kelas</p>
            </div>

            <div class="flex gap-2.5 shrink-0">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-2.5 text-center min-w-[76px]">
                    <p class="text-lg md:text-xl font-bold leading-none" x-data x-init="$el.textContent=0" data-count="{{ $conversations->count() }}">
                        {{ $conversations->count() }}
                    </p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Percakapan</p>
                </div>
                @php $totalUnread = $conversations->sum(fn($c) => $c->unreadCountFor(auth()->id())); @endphp
                <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-2.5 text-center min-w-[76px] relative">
                    @if ($totalUnread > 0)
                        <span class="absolute -top-1.5 -right-1.5 w-3 h-3 rounded-full bg-[#EF4444] animate-ping"></span>
                        <span class="absolute -top-1.5 -right-1.5 w-3 h-3 rounded-full bg-[#EF4444]"></span>
                    @endif
                    <p class="text-lg md:text-xl font-bold leading-none">{{ $totalUnread }}</p>
                    <p class="text-[10px] md:text-xs text-indigo-100 mt-1">Belum dibaca</p>
                </div>
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

    <div class="grid md:grid-cols-5 gap-6">
        {{-- ============ KOLOM KIRI: DAFTAR PERCAKAPAN ============ --}}
        <div class="md:col-span-3 rise-in" style="animation-delay: 60ms">

            {{-- Search + Tabs --}}
            <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-3 md:p-4 mb-4">
                <form method="GET" class="relative mb-3">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau isi pesan..."
                           class="w-full border border-gray-200 bg-gray-50 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:bg-white focus:border-transparent transition">
                </form>

                <div class="flex gap-1.5">
                    <button type="button" @click="tab = 'all'"
                            :class="tab === 'all' ? 'bg-[#4F46E5] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all">
                        Semua
                    </button>
                    <button type="button" @click="tab = 'unread'"
                            :class="tab === 'unread' ? 'bg-[#4F46E5] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100'"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1.5">
                        Belum Dibaca
                        @if ($totalUnread > 0)
                            <span :class="tab === 'unread' ? 'bg-white/25' : 'bg-[#EF4444] text-white'" class="rounded-full w-4 h-4 flex items-center justify-center text-[10px] leading-none">{{ $totalUnread }}</span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Daftar Percakapan --}}
            <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl divide-y divide-gray-100 overflow-hidden">
                @forelse ($conversations as $i => $c)
                    @php
                        $other = $c->participants->firstWhere('id', '!=', auth()->id());
                        $unread = $c->unreadCountFor(auth()->id());
                        $palette = ['from-[#4F46E5] to-[#818CF8]', 'from-[#7C3AED] to-[#a78bfa]', 'from-[#0EA5E9] to-[#7dd3fc]', 'from-[#3B82F6] to-[#93c5fd]'];
                    @endphp
                    <a href="{{ route('guru.messages.show', $c) }}"
                       x-show="tab === 'all' || {{ $unread }} > 0"
                       class="flex items-center gap-3 px-4 md:px-5 py-4 hover:bg-indigo-50/40 transition-colors duration-150 group rise-in"
                       style="animation-delay: {{ $i * 50 }}ms">
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 md:w-12 md:h-12 rounded-full bg-gradient-to-br {{ $palette[$i % 4] }} text-white flex items-center justify-center font-semibold text-sm shadow-sm">
                                {{ strtoupper(substr($other->name ?? '-', 0, 1)) }}
                            </div>
                            @if ($unread > 0)
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#10B981] border-2 border-white"></span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold text-gray-800 text-sm md:text-base truncate">{{ $other->name ?? '-' }}</p>
                                @if ($c->lastMessage?->created_at)
                                    <span class="text-[11px] text-gray-400 shrink-0">{{ $c->lastMessage->created_at->diffForHumans(null, true) }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 truncate {{ $unread > 0 ? 'font-medium text-gray-700' : '' }}">
                                {{ $c->lastMessage->content ?? '(lampiran)' }}
                            </p>
                        </div>
                        @if ($unread > 0)
                            <span class="text-xs bg-[#4F46E5] text-white rounded-full w-5 h-5 flex items-center justify-center leading-none shrink-0 font-semibold">{{ $unread }}</span>
                        @else
                            <svg class="w-4 h-4 text-gray-300 shrink-0 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        @endif
                    </a>
                @empty
                    <div class="px-5 py-14 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400">Belum ada percakapan.</p>
                        <p class="text-xs text-gray-400 mt-1">Mulai obrolan baru dari panel di samping.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============ KOLOM KANAN: MULAI PERCAKAPAN & SIARAN ============ --}}
        <div class="md:col-span-2 space-y-4 rise-in" style="animation-delay: 100ms" x-data="{ activeClass: {{ $classes->first()->id ?? 'null' }} }">

            {{-- Featured: Siaran Kelas --}}
            <div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-br from-[#3730A3] to-[#1E1B4B] p-5 text-white">
                <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
                <div class="relative flex items-center gap-3 mb-3">
                    <div class="relative w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center shrink-0">
                        <span class="absolute inset-0 rounded-2xl bg-white/20" style="animation: soft-pulse 2.8s ease-in-out infinite"></span>
                        <svg class="w-5 h-5 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">Siaran Kelas</p>
                        <p class="text-xs text-indigo-200">Kirim satu pesan ke seluruh siswa</p>
                    </div>
                </div>

                @if ($classes->count() > 0)
                    <div class="flex gap-1.5 overflow-x-auto scrollbar-none pb-1 mb-3 relative">
                        @foreach ($classes as $class)
                            <button type="button" @click="activeClass = {{ $class->id }}"
                                    :class="activeClass === {{ $class->id }} ? 'bg-white text-[#3730A3]' : 'bg-white/10 text-indigo-100 hover:bg-white/20'"
                                    class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all whitespace-nowrap">
                                {{ $class->name }}
                            </button>
                        @endforeach
                    </div>

                    @foreach ($classes as $class)
                        <form method="POST" action="{{ route('guru.messages.broadcast.store', $class) }}"
                              x-show="activeClass === {{ $class->id }}" x-cloak
                              x-data="{ sending: false }" @submit="sending = true"
                              class="relative flex gap-2">
                            @csrf
                            <input type="text" name="content" placeholder="Tulis pengumuman untuk {{ $class->name }}..."
                                   class="flex-1 border border-white/15 bg-white/10 placeholder-indigo-200 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-white/40 focus:border-transparent transition">
                            <button type="submit" :disabled="sending"
                                    class="inline-flex items-center gap-1.5 text-sm bg-white text-[#3730A3] px-4 py-2.5 rounded-xl font-semibold transition-all hover:-translate-y-0.5 active:scale-95 disabled:opacity-70 disabled:pointer-events-none shrink-0">
                                <svg x-show="!sending" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                <svg x-show="sending" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="sending ? '' : 'Kirim'" class="hidden sm:inline"></span>
                            </button>
                        </form>
                    @endforeach
                @else
                    <p class="text-xs text-indigo-200">Belum ada kelas yang diampu.</p>
                @endif
            </div>

            {{-- Mulai Percakapan Baru --}}
            <div class="bg-white border border-gray-200 rounded-2xl md:rounded-3xl p-4 md:p-5">
                <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-sm md:text-base">
                    <svg class="w-5 h-5 text-[#4F46E5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Mulai Percakapan Baru
                </h2>

                <div class="max-h-80 overflow-y-auto pr-1 -mr-1 space-y-4">
                    @foreach ($classes as $class)
                        <div>
                            <p class="text-xs font-semibold text-[#4338CA] bg-indigo-50/60 inline-block px-2 py-0.5 rounded-md mb-2">{{ $class->name }}</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($class->enrollments as $enrollment)
                                    <form method="POST" action="{{ route('guru.messages.start', $enrollment->student->user->id) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 text-xs md:text-sm text-[#4F46E5] bg-indigo-50 hover:bg-indigo-100 hover:-translate-y-0.5 px-3 py-1.5 rounded-lg transition-all duration-150">
                                            <span class="w-4 h-4 rounded-full bg-[#4F46E5]/15 flex items-center justify-center text-[9px] font-bold">
                                                {{ strtoupper(substr($enrollment->student->user->name ?? '-', 0, 1)) }}
                                            </span>
                                            {{ $enrollment->student->user->name ?? '-' }}
                                        </button>
                                    </form>
                                @empty
                                    <p class="text-xs text-gray-400">Belum ada siswa terdaftar.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes rise-in {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes soft-pulse {
    0%, 100% { opacity: .35; transform: scale(1); }
    50% { opacity: 0; transform: scale(1.35); }
}
.rise-in { animation: rise-in .5s cubic-bezier(.16,1,.3,1) both; }
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
[x-cloak] { display: none !important; }
</style>
@endsection