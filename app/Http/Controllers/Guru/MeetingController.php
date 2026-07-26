<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeetingRequest;
use App\Models\Meeting;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MeetingController extends Controller
{
    /**
     * Daftar pertemuan dalam satu kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $meetings = $class->meetings()
            ->withCount('materials')
            ->orderBy('order')
            ->get();

        return view('guru.meetings.index', compact('class', 'meetings'));
    }

    /**
     * Form buat pertemuan baru.
     */
    public function create(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $nextOrder = $class->meetings()->max('order') + 1;

        return view('guru.meetings.create', compact('class', 'nextOrder'));
    }

    /**
     * Simpan pertemuan baru — nomor urut otomatis (ke-N).
     */
    public function store(StoreMeetingRequest $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $nextOrder = $class->meetings()->max('order') + 1;

        $class->meetings()->create([
            ...$request->validated(),
            'order' => $nextOrder,
        ]);

        return redirect()
            ->route('guru.classes.meetings.index', $class)
            ->with('success', "Pertemuan ke-{$nextOrder} berhasil dibuat.");
    }

    /**
     * Form edit pertemuan — nomor urut tidak dapat diubah.
     */
    public function edit(SchoolClass $class, Meeting $meeting): View
    {
        $this->authorizeTeacher($class);

        return view('guru.meetings.edit', compact('class', 'meeting'));
    }

    /**
     * Update topik, deskripsi, tanggal, dan status pertemuan.
     * Nomor urut (order) sengaja tidak divalidasi/diupdate untuk menjaga konsistensi.
     */
    public function update(StoreMeetingRequest $request, SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $meeting->update($request->validated());

        return redirect()
            ->route('guru.classes.meetings.index', $class)
            ->with('success', 'Pertemuan berhasil diperbarui.');
    }

    /**
     * Hapus pertemuan (soft delete).
     */
    public function destroy(SchoolClass $class, Meeting $meeting): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $meeting->delete();

        return redirect()
            ->route('guru.classes.meetings.index', $class)
            ->with('success', 'Pertemuan berhasil dihapus.');
    }

    // ── Private helper ────────────────────────────────

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
