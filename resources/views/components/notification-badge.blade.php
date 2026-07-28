{{-- resources/views/components/notification-badge.blade.php --}}
<div class="relative" x-data="{
    open: false,
    count: 0,
    items: [],
    loading: false,
    error: false,
    fetchCount() {
        fetch('{{ route('notifications.unread-count') }}')
            .then(r => r.json())
            .then(data => this.count = data.total)
            .catch(() => {});
    },
    fetchItems() {
        this.loading = true;
        this.error = false;
        fetch('{{ route('notifications.recent') }}')
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => { this.items = data.items; this.loading = false; })
            .catch(err => { console.error('Gagal muat notifikasi:', err); this.loading = false; this.error = true; });
    },
    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.fetchItems();
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    },
    close() {
        this.open = false;
        document.body.style.overflow = '';
    },
    clickItem(item) {
        if (item.read_url) {
            fetch(item.read_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
            }).then(() => {
                window.location.href = item.target_url || '{{ route('notifications.index') }}';
            }).catch(() => {
                window.location.href = item.target_url || '{{ route('notifications.index') }}';
            });
            return;
        }
        if (item.target_url) {
            window.location.href = item.target_url;
        }
    },
    iconMeta(item) {
        // Cocokkan berdasarkan field `type` dari backend jika ada, fallback ke tebak-tebak dari judul
        const type = (item.type || '').toLowerCase();
        const title = (item.title || '').toLowerCase();

        if (type.includes('violation') || title.includes('pelanggaran') || title.includes('keluar dari halaman')) {
            return {
                bg: 'from-[#EF4444] to-[#F87171]',
                path: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
            };
        }
        if (type.includes('quiz') || type.includes('nilai') || title.includes('nilai kuis') || title.includes('nilai')) {
            return {
                bg: 'from-[#4F46E5] to-[#818CF8]',
                path: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            };
        }
        if (type.includes('announcement') || type.includes('pengumuman') || title.includes('pengumuman')) {
            return {
                bg: 'from-[#D97706] to-[#FBBF24]',
                path: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
            };
        }
        if (type.includes('assignment') || type.includes('tugas') || title.includes('tugas')) {
            return {
                bg: 'from-[#059669] to-[#34D399]',
                path: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            };
        }
        // default
        return {
            bg: 'from-gray-400 to-gray-500',
            path: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        };
    },
}" x-init="fetchCount(); setInterval(fetchCount, 30000)">

    <button @click="toggle()" class="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-indigo-50 transition-all duration-200 group">
        <svg class="w-5 h-5 text-gray-500 group-hover:text-[#4F46E5] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="count > 0" x-cloak
              x-text="count > 9 ? '9+' : count"
              class="absolute -top-1 -right-1 bg-gradient-to-br from-[#EF4444] to-[#F87171] text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center border-2 border-white animate-pulse shadow-sm"></span>
    </button>

    {{-- Backdrop: hanya efektif di mobile (bottom sheet), transparan di desktop --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-[2px] z-40 md:bg-transparent md:backdrop-blur-0"></div>

    <div x-show="open" x-cloak
         @click.outside="close()"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
         class="fixed inset-x-0 bottom-0 z-50 w-full max-h-[80vh] rounded-t-3xl
                md:absolute md:inset-auto md:right-0 md:bottom-auto md:top-full md:mt-2.5 md:w-80 md:max-h-[28rem] md:rounded-2xl
                bg-white border border-gray-100 shadow-2xl shadow-indigo-200/40 overflow-hidden flex flex-col">

        {{-- Handle bar, cuma tampil di mobile --}}
        <div class="flex justify-center pt-2.5 pb-1 md:hidden shrink-0">
            <div class="w-10 h-1.5 rounded-full bg-gray-200"></div>
        </div>

        {{-- Header --}}
        <div class="px-4 py-3 md:py-3.5 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-white flex items-center justify-between shrink-0">
            <span class="font-bold text-gray-900 text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifikasi
            </span>
            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('notifications.read-all') }}" x-on:submit="close()">
                    @csrf
                    <button type="submit" class="text-[11px] text-gray-400 hover:text-[#4F46E5] font-semibold hover:underline transition-colors">Tandai semua dibaca</button>
                </form>
                <button @click="close()" class="md:hidden w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
            <template x-if="loading">
                <div class="px-4 py-8 text-center">
                    <svg class="w-5 h-5 text-indigo-300 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-gray-400">Memuat...</p>
                </div>
            </template>
            <template x-if="!loading && error">
                <div class="px-4 py-8 text-center">
                    <svg class="w-6 h-6 text-red-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <p class="text-sm text-[#EF4444] font-medium">Gagal memuat notifikasi.</p>
                    <p class="text-xs text-gray-400 mt-0.5">Coba refresh halaman.</p>
                </div>
            </template>
            <template x-if="!loading && !error && items.length === 0">
                <div class="px-4 py-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-sm text-gray-400">Belum ada notifikasi.</p>
                </div>
            </template>
            <template x-for="item in items" :key="item.id">
                <button type="button" @click="clickItem(item)"
                        class="flex items-start gap-2.5 w-full text-left px-4 py-3.5 md:py-3 hover:bg-indigo-50/50 transition-all duration-150"
                        :class="!item.read ? 'bg-gradient-to-r from-indigo-50/50 to-white' : ''">
                    <span class="relative w-9 h-9 md:w-8 md:h-8 rounded-full shrink-0 flex items-center justify-center mt-0.5 bg-gradient-to-br shadow-sm"
                          :class="iconMeta(item).bg">
                        <svg class="w-4 h-4 md:w-3.5 md:h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="iconMeta(item).path"/>
                        </svg>
                        <span x-show="!item.read" class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-[#EF4444] border-2 border-white"></span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm" :class="!item.read ? 'font-bold text-gray-900' : 'font-medium text-gray-600'" x-text="item.title"></p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="item.message"></p>
                        <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="item.time"></span>
                        </p>
                    </div>
                </button>
            </template>
        </div>

        <div class="px-4 py-3 md:py-2.5 border-t border-gray-100 flex items-center justify-between bg-gray-50/70 shrink-0"
             style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
            <a href="{{ route('notifications.index') }}" class="text-xs text-[#4F46E5] hover:text-[#4338CA] font-semibold hover:underline transition-colors">Semua notifikasi</a>
            <a href="{{ route('announcements.index') }}" class="text-xs text-[#4F46E5] hover:text-[#4338CA] font-semibold hover:underline transition-colors">Semua pengumuman</a>
        </div>
    </div>
</div>