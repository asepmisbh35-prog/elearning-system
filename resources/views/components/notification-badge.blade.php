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
        if (this.open) this.fetchItems();
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
}   
}" x-init="fetchCount(); setInterval(fetchCount, 30000)" @click.outside="open = false">

    <button @click="toggle()" class="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-indigo-50 transition-all duration-200 group">
        <svg class="w-5 h-5 text-gray-500 group-hover:text-[#4F46E5] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="count > 0" x-cloak
              x-text="count > 9 ? '9+' : count"
              class="absolute -top-1 -right-1 bg-gradient-to-br from-[#EF4444] to-[#F87171] text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center border-2 border-white animate-pulse shadow-sm"></span>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="absolute right-0 mt-2.5 w-[calc(100vw-2rem)] max-w-80 sm:w-80 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-indigo-200/40 z-50 overflow-hidden">

        {{-- Header dengan aksen gradient tipis --}}
        <div class="px-4 py-3.5 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-white flex items-center justify-between">
            <span class="font-bold text-gray-900 text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifikasi
            </span>
            <form method="POST" action="{{ route('notifications.read-all') }}" x-on:submit="open = false">
                @csrf
                <button type="submit" class="text-[11px] text-gray-400 hover:text-[#4F46E5] font-semibold hover:underline transition-colors">Tandai semua dibaca</button>
            </form>
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
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
                        class="flex items-start gap-2.5 w-full text-left px-4 py-3 hover:bg-indigo-50/50 transition-all duration-150"
                        :class="!item.read ? 'bg-gradient-to-r from-indigo-50/50 to-white' : ''">
                    <span class="relative w-8 h-8 rounded-full shrink-0 flex items-center justify-center mt-0.5"
                          :class="!item.read ? 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8]' : 'bg-gray-100'">
                        <svg class="w-3.5 h-3.5" :class="!item.read ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
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

        <div class="px-4 py-2.5 border-t border-gray-100 flex items-center justify-between bg-gray-50/70">
            <a href="{{ route('notifications.index') }}" class="text-xs text-[#4F46E5] hover:text-[#4338CA] font-semibold hover:underline transition-colors">Semua notifikasi</a>
            <a href="{{ route('announcements.index') }}" class="text-xs text-[#4F46E5] hover:text-[#4338CA] font-semibold hover:underline transition-colors">Semua pengumuman</a>
        </div>
    </div>
</div>