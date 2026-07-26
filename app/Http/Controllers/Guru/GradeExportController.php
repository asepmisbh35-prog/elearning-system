<?php

namespace App\Http\Controllers\Guru;

use App\Exports\ClassGradesExport;
use App\Http\Controllers\Controller;
use App\Models\KkmSetting;
use App\Models\SchoolClass;
use App\Services\GradeCalculator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GradeExportController extends Controller
{
    public function excel(SchoolClass $class)
    {
        $this->authorizeTeacher($class);

        $filename = 'nilai-' . \Str::slug($class->name) . '.xlsx';

        return Excel::download(new ClassGradesExport($class), $filename);
    }

    public function pdf(SchoolClass $class)
    {
        $this->authorizeTeacher($class);

        $components = $class->gradeComponents;
        $students = $class->students()->with('user')->get();
        $kkm = KkmSetting::activeFor($class);

        $rows = $students->map(function ($student) use ($components, $class) {
            $scores = $components->mapWithKeys(function ($component) use ($student) {
                $grade = \App\Models\Grade::where('grade_component_id', $component->id)
                    ->where('student_id', $student->id)
                    ->first();
                return [$component->id => $grade?->score];
            });

            $final = GradeCalculator::finalGradeFor($class, $student);

            return [
                'student' => $student,
                'scores'  => $scores,
                'final'   => $final,
            ];
        });

        $pdf = Pdf::loadView('guru.grades.export-pdf', compact('class', 'components', 'rows', 'kkm'));

        return $pdf->download('nilai-' . \Str::slug($class->name) . '.pdf');
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
