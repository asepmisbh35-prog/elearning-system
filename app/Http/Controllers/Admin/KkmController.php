<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KkmSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KkmController extends Controller
{
    /**
     * Halaman kelola KKM sekolah (histori + form set baru).
     */
    public function index(): View
    {
        $history = KkmSetting::whereNull('school_class_id')
            ->with('setter')
            ->orderByDesc('effective_from')
            ->get();

        $current = KkmSetting::activeFor(new \App\Models\SchoolClass()); // dummy, karena null class_id fallback ke sekolah
        // Lebih akurat: ambil langsung KKM sekolah aktif tanpa perlu instance SchoolClass
        $currentSchoolKkm = KkmSetting::whereNull('school_class_id')
            ->where('effective_from', '<=', now())
            ->orderByDesc('effective_from')
            ->first();

        return view('admin.kkm.index', compact('history', 'currentSchoolKkm'));
    }

    /**
     * Set KKM sekolah baru (prospektif, tidak mengubah histori).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'value'            => ['required', 'integer', 'min:0', 'max:100'],
            'effective_from'   => ['required', 'date'],
            'apply_to_history' => ['nullable', 'boolean'],
        ]);

        KkmSetting::create([
            'school_class_id' => null,
            'value'           => $request->value,
            'set_by'          => Auth::id(),
            'effective_from'  => $request->apply_to_history ? now()->subYears(10) : $request->effective_from,
        ]);

        $message = $request->boolean('apply_to_history')
            ? 'KKM sekolah berhasil diubah dan diterapkan ke semua nilai historis.'
            : 'KKM sekolah berhasil diubah, berlaku untuk penilaian ke depan.';

        return redirect()->route('admin.kkm.index')->with('success', $message);
    }
}
