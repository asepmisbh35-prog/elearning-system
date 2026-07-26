<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function edit()
    {
        $setting = SchoolSetting::current();
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SchoolSetting::current();

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:100'],
            'logo'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        $setting->school_name = $validated['school_name'];

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $setting->logo_path = $request->file('logo')->store('school', 'public');
        }

        $setting->save();

        return back()->with('success', 'Identitas sekolah berhasil diperbarui.');
    }
}