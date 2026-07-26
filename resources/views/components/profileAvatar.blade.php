{{-- resources/views/components/profile-avatar.blade.php --}}
{{--
    Komponen avatar untuk sidebar / navbar.
    Penggunaan: <x-profile-avatar :user="auth()->user()" size="36" />
--}}
@props(['user', 'size' => 36])

@php
    $src = $user->photo
        ? Storage::url($user->photo)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=F97316&color=fff&size=' . ($size * 2);
@endphp

<img src="{{ $src }}"
     alt="{{ $user->name }}"
     width="{{ $size }}"
     height="{{ $size }}"
     style="border-radius:50%; object-fit:cover; border:2px solid #F97316;"
     {{ $attributes }}>