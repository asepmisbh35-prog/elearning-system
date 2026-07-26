<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Meeting;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\ContentBlock;

class MaterialController extends Controller
{
    public function index(SchoolClass $schoolClass): View
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        $meetings = $schoolClass->meetings()
            ->where('status', 'published')
            ->orderBy('order')
            ->with(['materials' => function ($query) use ($student) {
                $query->where('status', 'published')
                    ->orderBy('order')
                    ->with(['progresses' => function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                            ->whereNull('content_block_id');
                    }]);
            }])
            ->get();

        return view('siswa.materials.index', compact('schoolClass', 'meetings', 'student'));
    }

    public function show(SchoolClass $schoolClass, Meeting $meeting, Material $material): View
    {
        $student = Auth::user()->student;
        $this->authorizeEnrollment($schoolClass, $student);

        abort_if($material->isLockedFor($student), 403, 'Materi ini masih terkunci.');

        $material->load('contentBlocks', 'discussions.user', 'discussions.replies.user', 'meeting.schoolClass.teacher.user');

        $progress = MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'student_id' => $student->id, 'content_block_id' => null],
            ['status' => 'in_progress', 'progress_percent' => 0, 'started_at' => now()]
        );

        $blockProgresses = MaterialProgress::where('material_id', $material->id)
            ->where('student_id', $student->id)
            ->whereNotNull('content_block_id')
            ->get()
            ->keyBy('content_block_id');

        // status lock per block, dihitung sekali di sini (server-side)
        $blockLocks = $material->contentBlocks->mapWithKeys(function ($block) use ($student) {
            return [$block->id => $block->isLockedFor($student)];
        });

        $nextMaterial = $meeting->materials()
            ->where('status', 'published')
            ->where('order', '>', $material->order)
            ->orderBy('order')
            ->first();

        return view('siswa.materials.show', compact(
            'schoolClass',
            'meeting',
            'material',
            'progress',
            'nextMaterial',
            'student',
            'blockProgresses',
            'blockLocks'
        ));
    }

    public function updateProgress(Request $request, Material $material): JsonResponse
    {
        $student = Auth::user()->student;

        $request->validate([
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $progress = MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'student_id' => $student->id, 'content_block_id' => null],
            ['status' => 'in_progress', 'progress_percent' => 0, 'started_at' => now()]
        );

        $newPercentage = max($progress->progress_percent, $request->percentage);
        $isNowComplete = $newPercentage >= 100;

        $progress->update([
            'progress_percent' => $newPercentage,
            'status'           => $isNowComplete ? 'completed' : 'in_progress',
            'completed_at'     => $isNowComplete && ! $progress->completed_at ? now() : $progress->completed_at,
        ]);

        return response()->json([
            'percentage'   => $progress->progress_percent,
            'is_completed' => $progress->status === 'completed',
        ]);
    }

    public function markComplete(Material $material): JsonResponse
    {
        $student = Auth::user()->student;

        $progress = MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'student_id' => $student->id, 'content_block_id' => null],
            ['status' => 'in_progress', 'progress_percent' => 0, 'started_at' => now()]
        );

        $progress->update([
            'progress_percent' => 100,
            'status'           => 'completed',
            'completed_at'     => $progress->completed_at ?? now(),
        ]);

        return response()->json([
            'percentage'   => 100,
            'is_completed' => true,
        ]);
    }

    public function completeBlock(Material $material, ContentBlock $contentBlock): JsonResponse
    {
        $student = Auth::user()->student;

        abort_unless($contentBlock->material_id === $material->id, 404);

        $blockProgress = MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'student_id' => $student->id, 'content_block_id' => $contentBlock->id],
            ['status' => 'in_progress', 'progress_percent' => 0, 'started_at' => now()]
        );

        if ($blockProgress->status !== 'completed') {
            $blockProgress->update([
                'status'           => 'completed',
                'progress_percent' => 100,
                'completed_at'     => now(),
            ]);
        }

        $material->recalculateProgressFor($student);

        $materialProgress = MaterialProgress::where('material_id', $material->id)
            ->where('student_id', $student->id)
            ->whereNull('content_block_id')
            ->first();

        return response()->json([
            'block_id'             => $contentBlock->id,
            'block_completed'      => true,
            'material_percentage'  => $materialProgress->progress_percent,
            'material_completed'   => $materialProgress->status === 'completed',
        ]);
    }

    private function authorizeEnrollment(SchoolClass $schoolClass, $student): void
    {
        abort_unless($student, 403);

        $enrolled = $schoolClass->enrollments()
            ->where('student_id', $student->id)
            ->exists();

        abort_unless($enrolled, 403, 'Kamu belum bergabung di kelas ini.');
    }
}
