<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KkmSetting;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KkmController extends Controller
{
    /**
     * Halaman kelola KKM override untuk 1 kelas.
     */
    public function index(SchoolClass $class): View
    {
        $this->authorizeTeacher($class);

        $history = KkmSetting::where('school_class_id', $class->id)
            ->orderByDesc('effective_from')
            ->get();

        $schoolDefault = KkmSetting::whereNull('school_class_id')
            ->where('effective_from', '<=', now())
            ->orderByDesc('effective_from')
            ->first();

        $activeKkm = KkmSetting::activeFor($class);

        return view('guru.grades.kkm.index', compact('class', 'history', 'schoolDefault', 'activeKkm'));
    }

    /**
     * Set KKM override untuk kelas ini (prospektif).
     */
    public function store(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorizeTeacher($class);

        $request->validate([
            'value'          => ['required', 'integer', 'min:0', 'max:100'],
            'effective_from' => ['required', 'date'],
        ]);

        KkmSetting::create([
            'school_class_id' => $class->id,
            'value'           => $request->value,
            'set_by'          => Auth::id(),
            'effective_from'  => $request->effective_from,
        ]);

        return redirect()
            ->route('guru.classes.kkm.index', $class)
            ->with('success', 'KKM kelas berhasil diperbarui, berlaku untuk penilaian ke depan.');
    }

    private function authorizeTeacher(SchoolClass $class): void
    {
        abort_unless(
            $class->teacher->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
