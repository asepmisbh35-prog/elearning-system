<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    // ── List Pengguna ──────────────────────────────────
    public function index(Request $request): View
    {
        $query = User::with(['teacher', 'student.rombel'])
            ->whereIn('role', ['guru', 'siswa']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('rombel_id')) {
            $query->whereHas('student', fn($q) => $q->where('rombel_id', $request->rombel_id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                fn($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $users   = $query->latest()->paginate(15)->withQueryString();
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'rombels'));
    }

    // ── Tambah Guru ────────────────────────────────────
    public function createGuru(): View
    {
        return view('admin.users.create-guru');
    }

    public function storeGuru(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'nip'     => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:100'],
            'phone'   => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'role'              => 'guru',
                'phone'             => $request->phone,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip'     => $request->nip,
                'subject' => $request->subject,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Akun guru {$request->name} berhasil ditambahkan.");
    }
    // ── Tambah Siswa ────────────────────────────────────
    public function createSiswa(): View
    {
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create-siswa', compact('rombels'));
    }

    public function storeSiswa(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', Password::min(8)->letters()->numbers()],
            'nisn'      => ['required', 'digits:10', 'unique:students,nisn'],
            'rombel_id' => ['nullable', 'exists:rombels,id'],
            'phone'     => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.unique'    => 'Email sudah digunakan.',
            'nisn.required'   => 'NISN wajib diisi.',
            'nisn.digits'     => 'NISN harus tepat 10 digit angka.',
            'nisn.unique'     => 'NISN sudah terdaftar.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'role'              => 'siswa',
                'phone'             => $request->phone,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            Student::create([
                'user_id'        => $user->id,
                'nisn'           => $request->nisn,
                'rombel_id'      => $request->rombel_id,
                'has_registered' => true,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Akun siswa {$request->name} berhasil ditambahkan.");
    }
    // ── Edit Pengguna ──────────────────────────────────
    public function edit(User $user): View
    {
        $user->load(['teacher', 'student.rombel']);
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'rombels'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($request->only(['name', 'phone']));

        if ($user->isGuru() && $user->teacher) {
            $user->teacher->update($request->only(['nip', 'subject']));
        }

        if ($user->isSiswa() && $user->student && $request->filled('rombel_id')) {
            $user->student->update(['rombel_id' => $request->rombel_id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data {$user->name} berhasil diperbarui.");
    }

    // ── Nonaktifkan / Aktifkan ─────────────────────────
    public function deactivate(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }
        $user->update(['is_active' => false]);
        return back()->with('success', "Akun {$user->name} berhasil dinonaktifkan.");
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);
        return back()->with('success', "Akun {$user->name} berhasil diaktifkan.");
    }

    // ── Import Siswa ───────────────────────────────────
    public function showImport(): View
    {
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.import', compact('rombels'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file'      => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'rombel_id' => ['required', 'exists:rombels,id'],
        ], [
            'file.required'      => 'File Excel wajib diupload.',
            'file.mimes'         => 'File harus berformat .xlsx atau .xls.',
            'rombel_id.required' => 'Rombel wajib dipilih.',
        ]);

        $rows    = \Maatwebsite\Excel\Facades\Excel::toArray([], $request->file('file'));
        $data    = $rows[0] ?? [];
        $success = 0;
        $failed  = 0;
        $errors  = [];

        foreach ($data as $index => $row) {
            if ($index === 0) continue; // skip header

            $nama = trim($row[0] ?? '');
            $nisn = trim($row[1] ?? '');

            if (empty($nama) || empty($nisn)) {
                $failed++;
                $errors[] = "Baris " . ($index + 1) . ": Nama atau NISN kosong.";
                continue;
            }

            if (! preg_match('/^\d{10}$/', $nisn)) {
                $failed++;
                $errors[] = "Baris " . ($index + 1) . ": NISN '{$nisn}' harus 10 digit.";
                continue;
            }

            if (Student::where('nisn', $nisn)->exists()) {
                $failed++;
                $errors[] = "Baris " . ($index + 1) . ": NISN '{$nisn}' sudah terdaftar.";
                continue;
            }

            Student::create([
                'rombel_id'      => $request->rombel_id,
                'nisn'           => $nisn,
                'name'           => $nama,
                'has_registered' => false,
            ]);
            $success++;
        }

        $msg = "Import selesai: {$success} berhasil, {$failed} gagal.";
        return redirect()->route('admin.users.index')
            ->with('success', $msg)
            ->with('import_errors', $errors);
    }
}
