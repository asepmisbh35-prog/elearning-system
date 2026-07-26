<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GradeComponent;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeComponentController extends Controller
{
    /**
     * Daftar komponen penilaian dalam satu kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $components = $class->gradeComponents()->get();
        $totalWeight = $components->sum('weight');

        return view('guru.grades.components.index', compact('class', 'components', 'totalWeight'));
    }

    /**
     * Simpan komponen penilaian baru.
     */
    public function store(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'weight' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'name.required'   => 'Nama komponen wajib diisi.',
            'weight.required' => 'Bobot wajib diisi.',
        ]);

        $currentTotal = $class->gradeComponents()->sum('weight');
        if ($currentTotal + $request->weight > 100) {
            return back()->withErrors([
                'weight' => "Total bobot akan menjadi {$currentTotal}% + {$request->weight}% = " . ($currentTotal + $request->weight) . "%, melebihi 100%.",
            ])->withInput();
        }

        $class->gradeComponents()->create($request->only('name', 'weight'));

        return redirect()
            ->route('guru.classes.grade-components.index', $class)
            ->with('success', 'Komponen penilaian berhasil ditambahkan.');
    }

    /**
     * Update komponen penilaian.
     */
    public function update(Request $request, SchoolClass $class, GradeComponent $gradeComponent): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'weight' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $currentTotal = $class->gradeComponents()->where('id', '!=', $gradeComponent->id)->sum('weight');
        if ($currentTotal + $request->weight > 100) {
            return back()->withErrors([
                'weight' => "Total bobot akan melebihi 100% (saat ini komponen lain sudah {$currentTotal}%).",
            ])->withInput();
        }

        $gradeComponent->update($request->only('name', 'weight'));

        return redirect()
            ->route('guru.classes.grade-components.index', $class)
            ->with('success', 'Komponen penilaian berhasil diperbarui.');
    }

    /**
     * Hapus komponen penilaian.
     */
    public function destroy(SchoolClass $class, GradeComponent $gradeComponent): RedirectResponse
    {
        $this->authorizeTeacher($class);

        if ($gradeComponent->assignments()->exists()) {
            return back()->withErrors([
                'delete' => 'Komponen ini sudah dipakai di tugas/kuis, tidak bisa dihapus.',
            ]);
        }

        $gradeComponent->delete();

        return redirect()
            ->route('guru.classes.grade-components.index', $class)
            ->with('success', 'Komponen penilaian berhasil dihapus.');
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
