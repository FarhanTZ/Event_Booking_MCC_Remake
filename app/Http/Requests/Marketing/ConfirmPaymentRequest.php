<?php

namespace App\Http\Requests\Marketing;

use App\DataTransferObjects\Marketing\ConfirmPaymentData;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmPaymentRequest extends FormRequest
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
            'payment_reference' => ['required', 'string', 'min:3', 'max:100'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'payment_reference.required' => 'Nomor referensi transaksi / nomor kuitansi STS (Surat Tanda Setor) wajib diisi.',
            'payment_reference.min' => 'Nomor referensi transaksi minimal 3 karakter.',
            'payment_reference.max' => 'Nomor referensi transaksi maksimal 100 karakter.',
            'payment_notes.max' => 'Catatan tambahan kasir maksimal 1000 karakter.',
        ];
    }

    /**
     * Transform validated request payload into typed DTO.
     */
    public function toDTO(string $bookingId, string $marketingUserId): ConfirmPaymentData
    {
        return new ConfirmPaymentData(
            bookingId: $bookingId,
            paymentReference: (string) $this->validated('payment_reference'),
            paymentNotes: $this->validated('payment_notes') ? (string) $this->validated('payment_notes') : null,
            marketingUserId: $marketingUserId,
        );
    }
}
