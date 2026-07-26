{{-- resources/views/materials/_discussion.blade.php
     Dipakai via: @include('materials._discussion', ['material' => $material, 'storeRoute' => 'guru.discussions.store', 'destroyRouteName' => 'guru.discussions.destroy'])
     $material harus sudah di-load dengan relasi 'discussions.user', 'discussions.replies.user' --}}

@php
    $isLocked = ! $material->meeting->schoolClass->is_active;
    $questions = $material->discussions->whereNull('parent_id')->sortByDesc('created_at');
@endphp

<div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 md:p-7 mt-6 shadow-sm shadow-indigo-100/40" x-data="{ openReply: null }">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-5">
        <h2 class="font-bold text-gray-900 flex items-center gap-2 text-sm md:text-base">
            <span class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-4 h-4 md:w-4.5 md:h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.5 0-2.9-.32-4.14-.89L3 20l1.05-3.15A7.94 7.94 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </span>
            Diskusi Materi
        </h2>
        @if ($isLocked)
            <span class="text-[11px] md:text-xs bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Terkunci (kelas diarsipkan)
            </span>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-100 text-emerald-700 text-sm px-4 py-2.5 rounded-xl mb-4 flex items-center gap-2 font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Form pertanyaan baru --}}
    @unless ($isLocked)
        <form method="POST" action="{{ route($storeRoute, $material) }}" class="mb-6">
            @csrf
            <div class="relative group">
                <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-r from-indigo-200 to-violet-200 opacity-0 group-focus-within:opacity-50 blur transition-opacity duration-300"></div>
                <textarea name="content" rows="2" placeholder="Ajukan pertanyaan tentang materi ini..." required
                          class="relative w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 outline-none transition-all duration-200 bg-white"></textarea>
            </div>
            <button type="submit" class="mt-2.5 inline-flex items-center gap-1.5 bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white text-sm font-semibold px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-indigo-300/50 hover:-translate-y-0.5 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Pertanyaan
            </button>
        </form>
    @endunless

    {{-- Daftar pertanyaan + balasan --}}
    <div class="space-y-4">
        @forelse ($questions as $q)
            <div class="border-2 border-gray-100 rounded-2xl p-4 md:p-5 hover:border-indigo-100 transition-colors duration-200">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 text-[#4338CA] flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(substr($q->user->name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $q->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $q->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if (!$isLocked && $q->canBeDeletedBy(auth()->user()))
                        <form method="POST" action="{{ route($destroyRouteName, $q) }}" onsubmit="return confirm('Hapus pertanyaan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-[#EF4444] hover:text-[#DC2626] font-medium hover:underline">Hapus</button>
                        </form>
                    @endif
                </div>
                <p class="text-sm text-gray-700 mt-2.5 leading-relaxed">{{ $q->content }}</p>

                {{-- Balasan --}}
                <div class="mt-3.5 ml-4 pl-4 border-l-2 border-indigo-100 space-y-3">
                    @foreach ($q->replies as $r)
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-700 flex items-center flex-wrap gap-1">
                                    {{ $r->user->name }}
                                    @if ($r->user_id === $material->meeting->schoolClass->teacher->user_id)
                                        <span class="text-[10px] bg-gradient-to-r from-[#4F46E5] to-[#818CF8] text-white px-2 py-0.5 rounded-full font-semibold">Guru</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $r->content }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $r->created_at->diffForHumans() }}</p>
                            </div>
                            @if (!$isLocked && $r->canBeDeletedBy(auth()->user()))
                                <form method="POST" action="{{ route($destroyRouteName, $r) }}" onsubmit="return confirm('Hapus balasan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-[#EF4444] hover:text-[#DC2626] font-medium hover:underline">Hapus</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Form balas --}}
                @unless ($isLocked)
                    <button type="button" @click="openReply = openReply === {{ $q->id }} ? null : {{ $q->id }}"
                            class="text-xs text-[#4F46E5] font-semibold mt-3 hover:text-[#4338CA] hover:underline flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17l-5-5m0 0l5-5m-5 5h12a4 4 0 014 4v1"/></svg>
                        Balas
                    </button>
                    <form x-show="openReply === {{ $q->id }}" x-cloak method="POST" action="{{ route($storeRoute, $material) }}" class="mt-2.5">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $q->id }}">
                        <textarea name="content" rows="2" placeholder="Tulis balasan..." required
                                  class="w-full border-2 border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:border-[#4F46E5] focus:ring-4 focus:ring-indigo-100 outline-none transition-all duration-200 bg-white"></textarea>
                        <button type="submit" class="mt-2 bg-gradient-to-r from-[#4338CA] to-[#4F46E5] text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl hover:shadow-md hover:shadow-indigo-300/50 transition-all">
                            Kirim Balasan
                        </button>
                    </form>
                @endunless
            </div>
        @empty
            <div class="text-center py-10">
                <div class="w-14 h-14 mx-auto rounded-full bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.5 0-2.9-.32-4.14-.89L3 20l1.05-3.15A7.94 7.94 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="text-sm text-gray-400">Belum ada pertanyaan di materi ini.</p>
            </div>
        @endforelse
    </div>
</div>