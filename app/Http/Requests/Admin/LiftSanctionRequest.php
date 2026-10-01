<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class LiftSanctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::SUPER_ADMIN;
    }

    public function rules(): array
    {
        return [
            'settlement_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
