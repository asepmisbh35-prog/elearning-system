<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Rombel;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClassController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->withCount('enrollments')
            ->with('rombel')
            ->latest()
            ->get();

        return view('guru.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();
        return view('guru.classes.create', compact('rombels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'subject'     => ['required', 'string', 'max:100'],
            'rombel_id'   => ['required', 'exists:rombels,id'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'      => 'Nama kelas wajib diisi.',
            'subject.required'   => 'Mata pelajaran wajib diisi.',
            'rombel_id.required' => 'Rombel wajib dipilih.',
        ]);

        $teacher = auth()->user()->teacher;

        $class = SchoolClass::create([
            'teacher_id'  => $teacher->id,
            'rombel_id'   => $request->rombel_id,
            'name'        => $request->name,
            'subject'     => $request->subject,
            'code'        => SchoolClass::generateCode($request->subject),
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('guru.classes.show', $class)
            ->with('success', "Kelas {$class->name} berhasil dibuat. Kode: {$class->code}");
    }

    public function show(SchoolClass $class): View
    {
        $this->authorizeClass($class);

        $class->load(['rombel', 'enrollments.student.user']);
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();

        return view('guru.classes.show', compact('class', 'rombels'));
    }

    public function edit(SchoolClass $class): View
    {
        $this->authorizeClass($class);
        $rombels = Rombel::where('is_active', true)->orderBy('name')->get();
        return view('guru.classes.edit', compact('class', 'rombels'));
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeClass($class);

        $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
        ]);

        $class->update($request->only(['name', 'description', 'is_active']));

        return redirect()->route('guru.classes.show', $class)
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function resetCode(SchoolClass $class): RedirectResponse
    {
        $this->authorizeClass($class);

        $newCode = SchoolClass::generateCode($class->subject);
        $class->update(['code' => $newCode]);

        return back()->with('success', "Kode kelas direset. Kode baru: {$newCode}");
    }

    public function qrCode(SchoolClass $class): View
    {
        $this->authorizeClass($class);

        $joinUrl = route('siswa.classes.join') . '?code=' . $class->code;

        $qr = QrCode::format('svg')
            ->size(280)
            ->errorCorrection('H')
            ->generate($joinUrl);

        return view('guru.classes.qr', compact('class', 'qr', 'joinUrl'));
    }

    public function removeStudent(SchoolClass $class, ClassEnrollment $enrollment): RedirectResponse
    {
        $this->authorizeClass($class);

        if ($enrollment->school_class_id !== $class->id) {
            abort(403);
        }

        $name = $enrollment->student->user->name ?? 'Siswa';
        $enrollment->delete();

        return back()->with('success', "{$name} berhasil dikeluarkan dari kelas.");
    }

    private function authorizeClass(SchoolClass $class): void
    {
        if ($class->teacher_id !== auth()->user()->teacher?->id) {
            abort(403, 'Kamu bukan pengajar kelas ini.');
        }
    }
}
