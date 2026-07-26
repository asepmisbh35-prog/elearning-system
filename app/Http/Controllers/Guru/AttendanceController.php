<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Meeting;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless($class->teacher->user_id === Auth::id(), 403);
    }

    public function show(SchoolClass $class, Meeting $meeting)
    {
        $this->authorizeTeacher($class);

        $currentSession = $meeting->attendanceSessions()->where('status', 'open')->latest()->first();

        $attendances = $currentSession
            ? $currentSession->attendances()->with('student.user')->get()
            : collect();

        $pastSessions = $meeting->attendanceSessions()
            ->where('status', 'closed')
            ->with(['attendances.student.user'])
            ->latest()
            ->get();

        return view('guru.attendance.show', compact('class', 'meeting', 'currentSession', 'attendances', 'pastSessions'));
    }

    public function store(StoreAttendanceRequest $request, SchoolClass $class, Meeting $meeting)
    {
        $this->authorizeTeacher($class);

        abort_if(
            $meeting->attendanceSessions()->where('status', 'open')->exists(),
            422,
            'Masih ada sesi absensi yang aktif untuk pertemuan ini.'
        );

        $today = now()->toDateString();

        $session = DB::transaction(function () use ($request, $meeting) {
            $session = AttendanceSession::create([
                'meeting_id' => $meeting->id,
                'code' => AttendanceSession::generateUniqueCode(),
                'opened_by' => Auth::id(),
                'check_in_start' => now()->setTimeFromTimeString($request->check_in_start),
                'check_in_end' => now()->setTimeFromTimeString($request->check_in_end),
                'status' => 'open',
            ]);

            // Bulk-insert baris alfa untuk semua siswa enrolled di kelas ini
            $studentIds = $meeting->schoolClass->enrollments()->pluck('student_id');

            $rows = $studentIds->map(fn($studentId) => [
                'attendance_session_id' => $session->id,
                'student_id' => $studentId,
                'status' => 'alfa',
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            if (! empty($rows)) {
                Attendance::insert($rows);
            }

            return $session;
        });

        return back()->with('success', "Sesi absensi dibuka. Kode: {$session->code}");
    }

    public function extend(StoreAttendanceRequest $request, AttendanceSession $session)
    {
        $this->authorizeTeacher($session->meeting->schoolClass);

        abort_unless($session->status === 'open', 422, 'Sesi sudah ditutup.');

        $session->update([
            'check_in_end' => now()->setTimeFromTimeString($request->check_in_end),
        ]);

        return back()->with('success', 'Waktu check-in diperpanjang.');
    }

    public function close(AttendanceSession $session)
    {
        $this->authorizeTeacher($session->meeting->schoolClass);

        abort_unless($session->status === 'open', 422, 'Sesi sudah ditutup.');

        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        // Siswa yang belum check-in & belum ajukan izin/sakit otomatis Alfa (sudah default alfa saat insert)

        return back()->with('success', 'Sesi absensi ditutup.');
    }

    public function updateStatus(StoreAttendanceRequest $request, Attendance $attendance)
    {
        $this->authorizeTeacher($attendance->session->meeting->schoolClass);

        abort_unless($attendance->session->status === 'closed', 422, 'Status hanya bisa diubah manual setelah sesi ditutup.');

        $from = $attendance->status;
        $to = $request->status;

        if ($from !== $to) {
            $attendance->logStatusChange($from, $to, Auth::id());
            $attendance->status = $to;
            $attendance->approval_status = null; // reset kalau sebelumnya pending
            $attendance->save();
        }

        return back()->with('success', 'Status kehadiran diperbarui.');
    }
}
