<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil sesuai role pengguna.
     */
    public function show(): View
    {
        $user = Auth::user();
        $data = ['user' => $user];

        if ($user->role === 'guru') {
            $teacher = $user->teacher()->with('schoolClasses.enrollments', 'schoolClasses.materials')->first();
            $data['teacher']      = $teacher;
            $data['totalSiswa']   = $teacher?->schoolClasses->sum(fn($c) => $c->enrollments->count()) ?? 0;
            $data['totalMateri']  = $teacher?->schoolClasses->sum(fn($c) => $c->materials->count()) ?? 0;
            $data['totalKelas']   = $teacher?->schoolClasses->count() ?? 0;
        }

        if ($user->role === 'siswa') {
            $student = $user->student()->with('rombel')->first();
            $data['student'] = $student;
        }

        return view('profile.show', $data);
    }

    /**
     * Update nama dan nomor telepon (Admin & Guru saja).
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Hanya admin & guru yang boleh edit nama/telepon
        if (!in_array($user->role, ['admin', 'guru'])) {
            abort(403);
        }

        $user->name  = $request->name;
        $user->phone = $request->phone;
        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Upload / ganti foto profil.
     * Berlaku untuk semua role.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'photo.max'    => 'Ukuran foto maksimal 2MB.',
            'photo.mimes'  => 'Format foto harus JPG atau PNG.',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika bukan default
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->photo = $path;
        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Hapus foto profil (kembali ke avatar default).
     */
    public function deletePhoto(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->photo = null;
        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Foto profil berhasil dihapus.');
    }

    /**
     * Ganti password dengan verifikasi password lama.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Password berhasil diperbarui.');
    }
}