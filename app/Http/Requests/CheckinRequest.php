<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->input('type')) {
            'checkin' => [
                'type' => ['required', 'in:checkin'],
                'code' => ['required', 'digits:6'],
            ],
            'izin', 'sakit' => [
                'type' => ['required', 'in:izin,sakit'],
                'attendance_session_id' => ['required', 'exists:attendance_sessions,id'],
                'keterangan' => ['required', 'string', 'max:1000'],
                'document' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
            ],
            default => [
                'type' => ['required', 'in:checkin,izin,sakit'],
            ],
        };
    }
}
