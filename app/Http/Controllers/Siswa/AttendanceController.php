<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckinRequest;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    private function authorizeEnrollment(SchoolClass $schoolClass): void
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        abort_unless(
            $schoolClass->enrollments()->where('student_id', $student->id)->exists(),
            403
        );
    }

    public function index(SchoolClass $schoolClass)
    {
        $this->authorizeEnrollment($schoolClass);

        $student = Auth::user()->student;

        $history = Attendance::where('student_id', $student->id)
            ->whereHas('session.meeting', fn($q) => $q->where('school_class_id', $schoolClass->id))
            ->with('session.meeting')
            ->latest()
            ->get();

        $percentage = Attendance::percentageFor($student->id, $schoolClass->id);

        // Sesi absensi yang sedang aktif (open) untuk kelas ini
        $activeSession = AttendanceSession::where('status', 'open')
            ->whereHas('meeting', fn($q) => $q->where('school_class_id', $schoolClass->id))
            ->with('meeting')
            ->first();

        $myAttendance = null;
        if ($activeSession) {
            $myAttendance = Attendance::where('attendance_session_id', $activeSession->id)
                ->where('student_id', $student->id)
                ->first();
        }

        return view('siswa.attendance.index', compact(
            'schoolClass',
            'history',
            'percentage',
            'activeSession',
            'myAttendance'
        ));
    }

    public function checkin(CheckinRequest $request, SchoolClass $schoolClass)
    {
        $this->authorizeEnrollment($schoolClass);

        if ($request->type === 'checkin') {
            return $this->handleCheckin($request, $schoolClass);
        }

        return $this->handleIzinSakit($request, $schoolClass);
    }

    private function handleCheckin(CheckinRequest $request, SchoolClass $schoolClass)
    {
        $student = Auth::user()->student;

        $session = AttendanceSession::where('code', $request->code)
            ->where('status', 'open')
            ->whereHas('meeting', fn($q) => $q->where('school_class_id', $schoolClass->id))
            ->first();

        abort_unless($session, 422, 'Kode absensi tidak valid atau sesi sudah ditutup.');
        abort_unless($session->isOpen(), 422, 'Waktu check-in sudah berakhir.');

        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        abort_unless($attendance, 403, 'Kamu tidak terdaftar di sesi ini.');
        abort_if($attendance->status === 'hadir', 422, 'Kamu sudah check-in.');

        $attendance->logStatusChange($attendance->status, 'hadir', Auth::id());
        $attendance->status = 'hadir';
        $attendance->checked_in_at = now();
        $attendance->save();

        return back()->with('success', 'Check-in berhasil.');
    }

    private function handleIzinSakit(CheckinRequest $request, SchoolClass $schoolClass)
    {
        $student = Auth::user()->student;

        $session = AttendanceSession::where('id', $request->attendance_session_id)
            ->whereHas('meeting', fn($q) => $q->where('school_class_id', $schoolClass->id))
            ->firstOrFail();

        $attendance = Attendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        abort_if($attendance->status === 'hadir', 422, 'Kamu sudah tercatat hadir di sesi ini.');

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('attendance-documents', 'public');
        }

        $attendance->logStatusChange($attendance->status, $request->type, Auth::id());
        $attendance->status = $request->type;
        $attendance->keterangan = $request->keterangan;
        $attendance->document_path = $documentPath ?? $attendance->document_path;
        $attendance->approval_status = 'pending';
        $attendance->save();

        return back()->with('success', 'Pengajuan ' . $request->type . ' berhasil dikirim, menunggu persetujuan guru.');
    }
}
