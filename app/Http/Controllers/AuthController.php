<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Http\Requests\Auth\RegisterSiswaRequest;


class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    // Proses login
    public function login(LoginRequest $request)
    {
        // Rate limiting: maks 5 percobaan per 15 menit per IP (sesuai spesifikasi 0.5)
        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($key, 900); // 15 menit = 900 detik
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->only('email'));
        }

        $user = Auth::user();

        // Cek akun aktif
        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
            ])->withInput($request->only('email'));
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    // Tampilkan halaman register siswa
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.register');
    }

    public function register(RegisterSiswaRequest $request)
    {
        $student = \App\Models\Student::where('nisn', $request->nisn)
            ->where('is_registered', false)
            ->whereNull('user_id')
            ->first();

        if (! $student) {
            return back()->withErrors([
                'nisn' => 'NISN tidak ditemukan atau sudah terdaftar. Hubungi admin.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Validasi nama (case-insensitive, toleransi spasi & typo minimal)
        $namaInput     = strtolower(trim($request->name));
        $namaTerdaftar = strtolower(trim($student->nama_lengkap));

        similar_text($namaInput, $namaTerdaftar, $percent);

        if ($percent < 80) {
            return back()->withErrors([
                'name' => 'Nama tidak sesuai data. Hubungi admin untuk klarifikasi.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Buat akun user
        $user = \App\Models\User::create([
            'name'      => $student->nama_lengkap,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => 'siswa',
            'is_active' => true,
        ]);

        // Update data student
        $student->update([
            'user_id'       => $user->id,
            'birth_date'    => $request->birth_date,
            'is_registered' => true,
        ]);

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return redirect()->route('login')->with(
            'success',
            'Registrasi berhasil! Cek email kamu untuk verifikasi akun sebelum login.'
        );
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    // Helper redirect berdasarkan role
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru'  => redirect()->route('guru.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
