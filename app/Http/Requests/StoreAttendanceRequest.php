<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route()->getActionMethod()) {
            'store' => [
                'check_in_start' => ['required', 'date_format:H:i'],
                'check_in_end' => ['required', 'date_format:H:i', 'after:check_in_start'],
            ],
            'extend' => [
                'check_in_end' => ['required', 'date_format:H:i'],
            ],
            'updateStatus' => [
                'status' => ['required', 'in:hadir,izin,sakit,alfa'],
            ],
            default => [],
        };
    }
}
