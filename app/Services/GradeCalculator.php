<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\GradeComponent;
use App\Models\SchoolClass;
use App\Models\Student;

class GradeCalculator
{
    /**
     * Hitung & simpan rekap otomatis untuk 1 komponen penilaian, 1 siswa.
     * Sumbernya: rata-rata semua Tugas + Kuis yang grade_component_id-nya sama.
     * Kalau sudah ada nilai manual untuk komponen ini, TIDAK ditimpa (manual selalu prioritas).
     */
    public static function recalculateComponent(GradeComponent $component, Student $student): ?Grade
    {
        $existing = Grade::where('grade_component_id', $component->id)
            ->where('student_id', $student->id)
            ->first();

        // Nilai manual tidak pernah ditimpa otomatis
        if ($existing && $existing->is_manual) {
            return $existing;
        }

        $scores = collect();

        // Ambil nilai dari Assignment yang terkait komponen ini
        foreach ($component->assignments as $assignment) {
            $submission = $assignment->submissionFor($student);
            if ($submission && $submission->isGraded()) {
                $percent = $assignment->max_score > 0
                    ? ($submission->score / $assignment->max_score) * 100
                    : 0;
                $scores->push($percent);
            }
        }

        // Ambil nilai dari Quiz yang terkait komponen ini
        $quizzes = \App\Models\Quiz::where('grade_component_id', $component->id)->get();
        foreach ($quizzes as $quiz) {
            $finalScore = $quiz->finalScoreFor($student);
            if (! is_null($finalScore)) {
                $scores->push($finalScore);
            }
        }

        if ($scores->isEmpty()) {
            return $existing; // belum ada data, biarkan apa adanya (mungkin null)
        }

        $average = round($scores->avg(), 2);

        return Grade::updateOrCreate(
            ['grade_component_id' => $component->id, 'student_id' => $student->id],
            ['score' => $average, 'is_manual' => false]
        );
    }

    /**
     * Hitung nilai akhir kelas (gabungan semua komponen sesuai bobot) untuk 1 siswa.
     */
    public static function finalGradeFor(SchoolClass $class, Student $student): ?float
    {
        $components = $class->gradeComponents()->get();

        if ($components->isEmpty()) {
            return null;
        }

        $totalWeighted = 0;
        $totalWeight = 0;

        foreach ($components as $component) {
            $grade = Grade::where('grade_component_id', $component->id)
                ->where('student_id', $student->id)
                ->first();

            if ($grade) {
                $totalWeighted += $grade->score * $component->weight;
                $totalWeight += $component->weight;
            }
        }

        if ($totalWeight === 0) {
            return null;
        }

        return round($totalWeighted / $totalWeight, 2);
    }

    /**
     * Refresh semua rekap otomatis untuk 1 kelas (dipanggil dari halaman Input Nilai).
     */
    public static function recalculateClass(SchoolClass $class): void
    {
        $components = $class->gradeComponents()->with('assignments')->get();
        $students = $class->students()->get();

        foreach ($components as $component) {
            foreach ($students as $student) {
                static::recalculateComponent($component, $student);
            }
        }
    }
}
