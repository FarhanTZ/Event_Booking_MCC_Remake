<?php

namespace App\Http\Requests\Marketing;

use App\DataTransferObjects\Marketing\ReviseBookingData;
use Illuminate\Foundation\Http\FormRequest;

class ReviseBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isMarketing() || $user->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'revision_notes' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'revision_notes.required' => 'Catatan evaluasi revisi wajib diisi oleh kurator marketing.',
            'revision_notes.min' => 'Catatan evaluasi revisi harus spesifik dan jelas (minimal 20 karakter).',
            'revision_notes.max' => 'Catatan evaluasi revisi maksimal 2000 karakter.',
        ];
    }

    /**
     * Transform validated request payload into typed DTO.
     */
    public function toDTO(string $bookingId, string $marketingUserId): ReviseBookingData
    {
        return new ReviseBookingData(
            bookingId: $bookingId,
            revisionNotes: (string) $this->validated('revision_notes'),
            marketingUserId: $marketingUserId,
            gracePeriodHours: 24,
        );
    }
}
