{{-- resources/views/admin/announcements/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Buat Pengumuman</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data"
          id="announcementForm" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pengumuman</label>
            <textarea name="content" rows="5" required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('content') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Target Penerima</label>
            <select name="target_type" id="targetType" onchange="toggleTargetFields()"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Pengguna</option>
                <option value="class">Satu Kelas</option>
                <option value="role">Role Tertentu</option>
                <option value="specific">Pengguna Spesifik</option>
            </select>
        </div>

        <div id="field-class" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas</label>
            <select name="school_class_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div id="field-role" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Role</label>
            <select name="target_role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
            </select>
        </div>

        <div id="field-specific" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pengguna</label>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-1">
                @foreach ($users as $u)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="target_user_ids[]" value="{{ $u->id }}" class="rounded text-indigo-600">
                        {{ $u->name }} <span class="text-xs text-gray-400">({{ $u->role }})</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Tayang (opsional)</label>
                <input type="datetime-local" name="publish_at"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kedaluwarsa (opsional)</label>
                <input type="datetime-local" name="expires_at"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
            Terbitkan Pengumuman
        </button>
    </form>
</div>

<script>
    function toggleTargetFields() {
        const type = document.getElementById('targetType').value;
        ['class', 'role', 'specific'].forEach(t => {
            document.getElementById('field-' + t).style.display = (t === type) ? 'block' : 'none';
        });
    }
    document.addEventListener('DOMContentLoaded', toggleTargetFields);
</script>
@endsection