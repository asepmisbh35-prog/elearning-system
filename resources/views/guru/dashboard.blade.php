@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="space-y-6">

    {{-- Selamat Datang --}}
    <div class="bg-gradient-to-r from-teal-600 to-teal-800 rounded-2xl p-6 text-white">
        <p class="text-teal-200 text-sm">Selamat datang,</p>
        <h2 class="text-2xl font-bold mt-1">{{ auth()->user()->name }}</h2>
        <p class="text-teal-200 text-sm mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-gray-500 text-sm">Selamat datang di dashboard guru. Fitur lengkap segera hadir.</p>
    </div>

</div>
@endsection