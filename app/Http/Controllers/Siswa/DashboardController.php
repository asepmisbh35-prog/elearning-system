<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Badge;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\StudentBadge;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Ikon per kata kunci mapel (fallback: buku umum)
    private const SUBJECT_ICONS = [
        'matematika'  => 'ti-math-symbols',
        'math'        => 'ti-math-symbols',
        'biologi'     => 'ti-leaf',
        'fisika'      => 'ti-atom-2',
        'kimia'       => 'ti-flask',
        'indonesia'   => 'ti-feather',
        'inggris'     => 'ti-world',
        'english'     => 'ti-world',
        'informatika' => 'ti-device-desktop',
        'komputer'    => 'ti-device-desktop',
        'tik'         => 'ti-device-desktop',
        'agama'       => 'ti-book',
        'olahraga'    => 'ti-ball-football',
        'penjas'      => 'ti-ball-football',
        'seni'        => 'ti-palette',
    ];

    // Palet cool-tone sesuai panduan UI (indigo/violet/sky/blue), di-cycle per mapel
    private const SUBJECT_COLORS = [
        ['key' => 'indigo', 'solid' => '#4F46E5', 'grad' => 'linear-gradient(135deg,#4F46E5,#818CF8)'],
        ['key' => 'violet', 'solid' => '#7C3AED', 'grad' => 'linear-gradient(135deg,#7C3AED,#C4B5FD)'],
        ['key' => 'sky',    'solid' => '#0284C7', 'grad' => 'linear-gradient(135deg,#0284C7,#7DD3FC)'],
        ['key' => 'blue',   'solid' => '#2563EB', 'grad' => 'linear-gradient(135deg,#2563EB,#93C5FD)'],
    ];

    private const BADGE_DEFS = [
        ['key' => 'streak_7',          'label' => 'Rajin 7 Hari',      'icon' => 'ti-flame',   'color_key' => 'indigo'],
        ['key' => 'high_achiever',     'label' => 'Nilai Cemerlang',   'icon' => 'ti-award',   'color_key' => 'violet'],
        ['key' => 'consistent_learner', 'label' => 'Belajar Konsisten', 'icon' => 'ti-trophy',  'color_key' => 'sky'],
        ['key' => 'explorer',          'label' => 'Penjelajah',        'icon' => 'ti-map-2',   'color_key' => 'blue'],
    ];

    public function index()
    {
        $student = Auth::user()->student;
        $classes = $this->enrolledClasses($student);

        return view('siswa.dashboard', [
            'totalClasses'  => $classes->count(),
            'totalSubjects' => $classes->pluck('subject')->unique()->count(),
        ]);
    }

    public function progress()
    {
        $student = Auth::user()->student;
        $classes = $this->enrolledClasses($student);

        $subjects = [];
        $index = 0;
        foreach ($classes->groupBy('subject') as $subject => $subjectClasses) {
            $scores = [];
            foreach ($subjectClasses as $class) {
                $score = $this->classScoreForStudent($class, $student);
                if ($score !== null) {
                    $scores[] = $score;
                }
            }

            $color = self::SUBJECT_COLORS[$index % count(self::SUBJECT_COLORS)];
            $subjects[] = [
                'subject'   => $subject,
                'progress'  => count($scores) ? round(array_sum($scores) / count($scores), 1) : 0,
                'has_data'  => count($scores) > 0,
                'icon'      => $this->iconForSubject($subject),
                'color'     => $color,
                'class_id'  => $subjectClasses->first()->id,
            ];
            $index++;
        }

        $withData = array_filter($subjects, fn($s) => $s['has_data']);
        $overall = count($withData)
            ? round(array_sum(array_column($withData, 'progress')) / count($withData), 1)
            : 0;

        $streak = $this->currentStreak($student);
        $badges = $this->evaluateBadges($student, $streak, $overall, $subjects, count($classes->pluck('subject')->unique()));
        $deadlines = $this->upcomingDeadlines($student, $classes);
        $recentActivity = $this->recentActivity($student);
        $studyMinutes = $this->studyMinutesLast30($student);

        return response()->json([
            'overall'         => $overall,
            'subjects'        => array_values($subjects),
            'streak'          => $streak,
            'badges'          => $badges,
            'deadlines'       => $deadlines,
            'recent_activity' => $recentActivity,
            'study'           => $studyMinutes,
            'updated_at'      => now()->translatedFormat('H:i:s'),
        ]);
    }

    /**
     * Dipanggil tiap ~60 detik dari dashboard selagi tab aktif/terlihat.
     * Mencatat 1 baris = kira-kira 1 menit belajar aktif di halaman ini.
     */
    public function heartbeat(Request $request)
    {
        $student = Auth::user()->student;
        if (! $student) {
            return response()->json(['status' => 'ignored']);
        }

        $lastPing = StudySession::where('student_id', $student->id)
            ->latest('occurred_at')
            ->first();

        // Throttle: jangan catat kalau ping terakhir kurang dari 50 detik lalu (hindari duplikat)
        if (! $lastPing || $lastPing->occurred_at->diffInSeconds(now()) >= 50) {
            StudySession::create([
                'student_id'  => $student->id,
                'occurred_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function enrolledClasses($student)
    {
        if (! $student) {
            return collect();
        }

        return SchoolClass::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })
            ->with('gradeComponents')
            ->get();
    }

    private function classScoreForStudent(SchoolClass $class, $student): ?float
    {
        $components = $class->gradeComponents;
        if ($components->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedScore = 0;
        $hasAnyGrade = false;

        foreach ($components as $component) {
            $avg = Grade::where('grade_component_id', $component->id)
                ->where('student_id', $student->id)
                ->avg('score');

            if ($avg === null) {
                continue;
            }

            $hasAnyGrade = true;
            $weightedScore += $avg * $component->weight;
            $totalWeight += $component->weight;
        }

        if (! $hasAnyGrade || $totalWeight <= 0) {
            return null;
        }

        return round($weightedScore / $totalWeight, 1);
    }

    private function iconForSubject(string $subject): string
    {
        $lower = strtolower($subject);
        foreach (self::SUBJECT_ICONS as $keyword => $icon) {
            if (str_contains($lower, $keyword)) {
                return $icon;
            }
        }
        return 'ti-book-2';
    }

    private function currentStreak($student): int
    {
        if (! $student) {
            return 0;
        }

        $gradeDates = Grade::where('student_id', $student->id)
            ->pluck('created_at')
            ->map(fn($d) => $d->format('Y-m-d'));

        $activityDates = ActivityLog::where('student_id', $student->id)
            ->pluck('occurred_at')
            ->map(fn($d) => $d->format('Y-m-d'));

        $days = $gradeDates->merge($activityDates)->unique()->sort()->values();

        if ($days->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $cursor = now()->format('Y-m-d');
        $daySet = $days->flip();

        while ($daySet->has($cursor)) {
            $streak++;
            $cursor = \Carbon\Carbon::parse($cursor)->subDay()->format('Y-m-d');
        }

        return $streak;
    }

    private function evaluateBadges($student, int $streak, float $overall, array $subjects, int $totalSubjects): array
    {
        if (! $student) {
            return ['earned' => [], 'locked_count' => count(self::BADGE_DEFS)];
        }

        $eligible = [
            'streak_7'           => $streak >= 7,
            'high_achiever'      => collect($subjects)->contains(fn($s) => $s['has_data'] && $s['progress'] >= 90),
            'consistent_learner' => $overall >= 80 && collect($subjects)->where('has_data', true)->count() >= 2,
            'explorer'           => $totalSubjects >= 3,
        ];

        $earned = [];
        foreach (self::BADGE_DEFS as $def) {
            if (! ($eligible[$def['key']] ?? false)) {
                continue;
            }

            $badge = Badge::firstOrCreate(['key' => $def['key']], $def);

            $studentBadge = StudentBadge::firstOrCreate(
                ['student_id' => $student->id, 'badge_id' => $badge->id],
                ['earned_at' => now()]
            );

            $earned[] = [
                'label' => $badge->label,
                'icon'  => $badge->icon,
                'color_key' => $badge->color_key,
            ];
        }

        return [
            'earned'       => $earned,
            'locked_count' => count(self::BADGE_DEFS) - count($earned),
        ];
    }

    private function upcomingDeadlines($student, $classes): array
    {
        if (! $student) {
            return [];
        }

        $classIds = $classes->pluck('id');

        $assignments = Assignment::whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->orderBy('due_date')
            ->with('schoolClass')
            ->get()
            ->filter(fn($a) => $a->isVisibleFor($student) && $a->submissionFor($student) === null)
            ->take(3);

        return $assignments->map(function ($a) {
            $index = crc32($a->schoolClass->subject) % 4;
            return [
                'title'   => $a->title,
                'subject' => $a->schoolClass->subject,
                'due'     => $a->due_date->translatedFormat('d M, H:i') . ' WIB',
                'urgent'  => now()->diffInHours($a->due_date, false) <= 24,
                'color'   => self::SUBJECT_COLORS[$index],
            ];
        })->values()->all();
    }

    private function recentActivity($student): array
    {
        if (! $student) {
            return [];
        }

        $fromLogs = ActivityLog::where('student_id', $student->id)
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get()
            ->map(fn($log) => [
                'description' => $log->description,
                'meta'        => ($log->subject ? $log->subject . ' · ' : '') . $log->occurred_at->diffForHumans(),
                'type'        => $log->type,
            ]);

        $fromGrades = Grade::where('student_id', $student->id)
            ->with('gradeComponent.schoolClass')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->filter(fn($g) => $g->gradeComponent && $g->gradeComponent->schoolClass)
            ->map(fn($g) => [
                'description' => 'Nilai baru: ' . $g->gradeComponent->name,
                'meta'        => $g->gradeComponent->schoolClass->subject . ' · ' . $g->created_at->diffForHumans(),
                'type'        => 'grade',
            ]);

        return $fromLogs->concat($fromGrades)
            ->take(5)
            ->values()
            ->all();
    }

    private function studyMinutesLast30($student): array
    {
        if (! $student) {
            return ['total' => 0, 'points' => []];
        }

        $sessions = StudySession::where('student_id', $student->id)
            ->where('occurred_at', '>=', now()->subMinutes(30))
            ->orderBy('occurred_at')
            ->get();

        return [
            'total'  => $sessions->count(),
            'points' => $sessions->map(fn($s) => $s->occurred_at->format('H:i'))->all(),
        ];
    }
}
