<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    // Daftar kelas yang diikuti siswa
    public function index()
    {
        $student = Auth::user()->student;

        $enrollments = ClassEnrollment::with([
            'schoolClass.teacher.user',
            'schoolClass.rombel'
        ])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view(
            'siswa.classes.index',
            compact('enrollments')
        );
    }

    // Form join kelas (GET)
    public function showJoin(Request $request)
    {
        // Prefill kode jika dari QR scan
        $code = strtoupper($request->query('code', ''));
        return view('siswa.classes.join', compact('code'));
    }

    // Proses join kelas (POST)
    public function join(Request $request)
    {
        $request->validate([
            'code' => [
                'required',
                'string',
                'min:3',
                'max:20'
            ],
        ], [
            'code.required' => 'Kode kelas wajib diisi.',
            'code.min' => 'Kode kelas terlalu pendek.',
        ]);

        $student = Auth::user()->student;

        // Cari kelas berdasarkan kode
        $class = SchoolClass::where(
            'code',
            strtoupper(trim($request->code))
        )
            ->where('is_active', true)
            ->first();

        if (!$class) {
            return back()
                ->withErrors(['code' =>
                'Kode tidak ditemukan atau kelas tidak aktif.'])
                ->withInput();
        }

        // Cek sudah terdaftar
        $sudahTerdaftar = ClassEnrollment::where([
            'school_class_id' => $class->id,
            'student_id'      => $student->id,
        ])->exists();

        if ($sudahTerdaftar) {
            return back()
                ->withErrors(['code' =>
                'Kamu sudah terdaftar di kelas ini.'])
                ->withInput();
        }

        ClassEnrollment::create([
            'school_class_id' => $class->id,
            'student_id'      => $student->id,
            'joined_at'       => now(),
        ]);

        return redirect()
            ->route('siswa.classes.show', $class)
            ->with(
                'success',
                'Berhasil bergabung ke kelas ' . $class->name . '!'
            );
    }

    // Detail kelas siswa
    public function show(SchoolClass $class)
    {
        $student = Auth::user()->student;

        $enrolled = ClassEnrollment::where([
            'school_class_id' => $class->id,
            'student_id'      => $student->id,
        ])->exists();

        if (!$enrolled) {
            abort(403, 'Kamu tidak terdaftar di kelas ini.');
        }

        $class->load(['teacher.user', 'rombel', 'enrollments']);

        $meetings = $class->meetings()
            ->where('status', 'published')
            ->orderBy('order')
            ->get();

        return view('siswa.classes.show', compact('class', 'meetings'));
    }
}
