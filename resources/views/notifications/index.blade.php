{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 md:py-8">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-br from-[#4F46E5] to-[#818CF8] flex items-center justify-center shrink-0 shadow-sm shadow-indigo-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </span>
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-tight">Notifikasi</h1>
                @php $unreadCount = $notifications->filter(fn($n) => is_null($n->read_at))->count(); @endphp
                <p class="text-xs md:text-sm text-gray-400" x-data x-show="true">
                    @if ($unreadCount > 0)
                        <span class="text-[#4F46E5] font-semibold">{{ $unreadCount }} belum dibaca</span>
                    @else
                        Semua sudah dibaca
                    @endif
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-xs md:text-sm text-gray-500 hover:text-[#4F46E5] font-medium px-3 py-2 rounded-xl hover:bg-indigo-50 transition-all duration-200 flex items-center gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="hidden sm:inline">Tandai semua dibaca</span>
                <span class="sm:hidden">Tandai semua</span>
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm font-medium">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl divide-y divide-gray-50 shadow-sm shadow-indigo-100/40 overflow-hidden">
        @forelse ($notifications as $notification)
            @php $isUnread = is_null($notification->read_at); @endphp
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="block {{ $isUnread ? 'bg-gradient-to-r from-indigo-50/50 to-white' : '' }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 md:px-5 py-4 hover:bg-indigo-50/40 transition-all duration-200 flex items-start gap-3 group">
                    <span class="relative w-9 h-9 md:w-10 md:h-10 rounded-full shrink-0 flex items-center justify-center mt-0.5
                                 {{ $isUnread ? 'bg-gradient-to-br from-[#4F46E5] to-[#818CF8]' : 'bg-gray-100' }}">
                        <svg class="w-4 h-4 md:w-4.5 md:h-4.5 {{ $isUnread ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if ($isUnread)
                            <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-[#EF4444] border-2 border-white animate-pulse"></span>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm {{ $isUnread ? 'font-bold text-gray-900' : 'font-medium text-gray-600' }}">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if ($isUnread)
                        <span class="text-[10px] font-semibold text-[#4F46E5] bg-indigo-100 px-2 py-0.5 rounded-full shrink-0 mt-1">Baru</span>
                    @endif
                </button>
            </form>
        @empty
            <div class="px-5 py-16 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-indigo-50 to-violet-50 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-400">Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection