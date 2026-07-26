@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
<p class="text-gray-500 mt-2">Selamat datang, {{ auth()->user()->name }}!</p>
@endsection