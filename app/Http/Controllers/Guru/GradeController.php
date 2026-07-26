<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\KkmSetting;
use App\Models\SchoolClass;
use App\Services\GradeCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Tabel nilai semua siswa x semua komponen dalam satu kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        // Refresh rekap otomatis dulu supaya data terbaru
        GradeCalculator::recalculateClass($class);

        $components = $class->gradeComponents()->get();
        $students = $class->students()->with('user')->get();
        $kkm = KkmSetting::activeFor($class);

        $grades = Grade::whereIn('grade_component_id', $components->pluck('id'))
            ->get()
            ->groupBy(fn($g) => $g->student_id . '-' . $g->grade_component_id);

        $rows = $students->map(function ($student) use ($components, $grades, $class) {
            $scores = $components->mapWithKeys(function ($component) use ($student, $grades) {
                $grade = $grades->get($student->id . '-' . $component->id)?->first();
                return [$component->id => $grade];
            });

            return [
                'student' => $student,
                'scores'  => $scores,
                'final'   => GradeCalculator::finalGradeFor($class, $student),
            ];
        });

        return view('guru.grades.index', compact('class', 'components', 'rows', 'kkm'));
    }

    /**
     * Input/update nilai manual untuk 1 siswa x 1 komponen (misal praktik, presentasi).
     */
    public function storeManual(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'student_id'         => ['required', 'exists:students,id'],
            'grade_component_id' => ['required', 'exists:grade_components,id'],
            'score'              => ['required', 'numeric', 'min:0', 'max:100'],
            'note'               => ['nullable', 'string', 'max:1000'],
        ]);

        Grade::updateOrCreate(
            [
                'grade_component_id' => $request->grade_component_id,
                'student_id'         => $request->student_id,
            ],
            [
                'score'     => $request->score,
                'is_manual' => true,
                'note'      => $request->note,
                'input_by'  => Auth::id(),
            ]
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Paksa hitung ulang semua rekap otomatis (tombol manual refresh).
     */
    public function recalculate(SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        GradeCalculator::recalculateClass($class);

        return back()->with('success', 'Rekap nilai berhasil diperbarui.');
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
