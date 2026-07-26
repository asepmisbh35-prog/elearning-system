<?php

namespace App\Exports;

use App\Models\SchoolClass;
use App\Services\GradeCalculator;
use App\Models\KkmSetting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassGradesExport implements FromArray, WithHeadings
{
    public function __construct(protected SchoolClass $class) {}

    public function headings(): array
    {
        $headings = ['Nama Siswa'];
        foreach ($this->class->gradeComponents as $component) {
            $headings[] = "{$component->name} ({$component->weight}%)";
        }
        $headings[] = 'Nilai Akhir';
        $headings[] = 'Predikat';
        return $headings;
    }

    public function array(): array
    {
        $components = $this->class->gradeComponents;
        $students = $this->class->students()->with('user')->get();
        $kkm = KkmSetting::activeFor($this->class);

        return $students->map(function ($student) use ($components, $kkm) {
            $row = [$student->user->name ?? $student->nama_lengkap ?? '-'];

            foreach ($components as $component) {
                $grade = \App\Models\Grade::where('grade_component_id', $component->id)
                    ->where('student_id', $student->id)
                    ->first();
                $row[] = $grade?->score ?? '-';
            }

            $final = GradeCalculator::finalGradeFor($this->class, $student);
            $row[] = $final ?? '-';
            $row[] = ! is_null($final) ? KkmSetting::predikat($final) : '-';

            return $row;
        })->toArray();
    }
}
