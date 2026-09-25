<?php

namespace App\Http\Requests\Marketing;

use App\DataTransferObjects\Marketing\RejectBookingData;
use Illuminate\Foundation\Http\FormRequest;

class RejectBookingRequest extends FormRequest
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
            'rejection_category' => ['required', 'string', 'in:regulasi_gedung,di_luar_ekraf,norma_ketertiban,overcapacity_ekstrem,lainnya'],
            'rejection_notes' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'rejection_category.required' => 'Kategori alasan penolakan wajib dipilih oleh marketing.',
            'rejection_category.in' => 'Kategori alasan penolakan yang dipilih tidak valid.',
            'rejection_notes.required' => 'Uraian catatan penolakan tertulis wajib diisi oleh marketing.',
            'rejection_notes.min' => 'Uraian catatan penolakan harus jelas dan spesifik (minimal 20 karakter).',
            'rejection_notes.max' => 'Uraian catatan penolakan maksimal 2000 karakter.',
        ];
    }

    /**
     * Transform validated request payload into typed DTO.
     */
    public function toDTO(string $bookingId, string $marketingUserId): RejectBookingData
    {
        return new RejectBookingData(
            bookingId: $bookingId,
            rejectionCategory: (string) $this->validated('rejection_category'),
            rejectionNotes: (string) $this->validated('rejection_notes'),
            marketingUserId: $marketingUserId,
        );
    }
}
