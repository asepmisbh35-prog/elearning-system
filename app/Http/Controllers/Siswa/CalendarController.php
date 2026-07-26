<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassEnrollment;
use App\Models\Meeting;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $student = Auth::user()->student;

        $classIds = ClassEnrollment::where('student_id', $student->id)->pluck('school_class_id');

        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $assignments = Assignment::whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->whereBetween('due_date', [$start, $end])
            ->with('schoolClass')
            ->get()
            ->map(fn($a) => [
                'date'  => $a->due_date->format('Y-m-d'),
                'time'  => $a->due_date->format('H:i'),
                'type'  => 'tugas',
                'title' => $a->title,
                'class' => $a->schoolClass->name ?? '',
                'url'   => Route::has('siswa.classes.assignments.show')
                    ? route('siswa.classes.assignments.show', [$a->school_class_id, $a->id])
                    : null,
            ]);

        $quizzes = Quiz::whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->whereBetween('access_end_at', [$start, $end])
            ->with('schoolClass')
            ->get()
            ->map(fn($q) => [
                'date'  => $q->access_end_at->format('Y-m-d'),
                'time'  => $q->access_end_at->format('H:i'),
                'type'  => 'kuis',
                'title' => $q->title,
                'class' => $q->schoolClass->name ?? '',
                'url'   => Route::has('siswa.classes.quizzes.index')
                    ? route('siswa.classes.quizzes.index', $q->school_class_id)
                    : null,
            ]);

        $meetings = Meeting::whereIn('school_class_id', $classIds)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end])
            ->with('schoolClass')
            ->get()
            ->map(fn($m) => [
                'date'  => $m->scheduled_at->format('Y-m-d'),
                'time'  => $m->scheduled_at->format('H:i'),
                'type'  => 'pertemuan',
                'title' => $m->topic,
                'class' => $m->schoolClass->name ?? '',
                'url'   => Route::has('siswa.classes.materials.index')
                    ? route('siswa.classes.materials.index', $m->school_class_id)
                    : null,
            ]);

        $events = $assignments->concat($quizzes)->concat($meetings)->groupBy('date');

        $weeks = $this->buildWeeks($start, $month, $events);

        return view('siswa.calendar.index', compact('weeks', 'month', 'year', 'start'));
    }

    /**
     * Bangun grid 7 kolom (Senin-Minggu) mengelilingi bulan berjalan.
     */
    private function buildWeeks(Carbon $firstDayOfMonth, int $month, $events): array
    {
        $startOffset = $firstDayOfMonth->dayOfWeekIso - 1; // 0 = Senin
        $cursor = $firstDayOfMonth->copy()->subDays($startOffset);

        $weeks = [];
        for ($w = 0; $w < 6; $w++) {
            $days = [];
            for ($d = 0; $d < 7; $d++) {
                $dateStr = $cursor->format('Y-m-d');
                $days[] = [
                    'date'    => $dateStr,
                    'day'     => $cursor->day,
                    'inMonth' => $cursor->month === $month,
                    'isToday' => $cursor->isToday(),
                    'events'  => $events->get($dateStr, collect()),
                ];
                $cursor->addDay();
            }
            $weeks[] = $days;
            if ($cursor->month !== $month && $w >= 4) {
                break;
            }
        }

        return $weeks;
    }
}
