<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KkmSetting;
use App\Models\SchoolClass;
use App\Services\GradeCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(SchoolClass $schoolClass): View
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        $components = $schoolClass->gradeComponents()->get();
        $kkm = KkmSetting::activeFor($schoolClass);

        $scores = $components->mapWithKeys(function ($component) use ($student) {
            $grade = \App\Models\Grade::where('grade_component_id', $component->id)
                ->where('student_id', $student->id)
                ->first();
            return [$component->id => $grade];
        });

        $final = GradeCalculator::finalGradeFor($schoolClass, $student);

        return view('siswa.grades.index', compact('schoolClass', 'components', 'scores', 'kkm', 'final'));
    }

    private function authorizeEnrollment(SchoolClass $schoolClass, $student): void
    {
        abort_unless($student, 403);
        $enrolled = $schoolClass->enrollments()->where('student_id', $student->id)->exists();
        abort_unless($enrolled, 403, 'Kamu belum bergabung di kelas ini.');
    }
}
