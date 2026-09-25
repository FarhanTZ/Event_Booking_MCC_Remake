<?php

namespace App\Http\Requests\Marketing;

use App\DataTransferObjects\Marketing\ApproveBookingData;
use Illuminate\Foundation\Http\FormRequest;

class ApproveBookingRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('nominal_amount') && is_string($this->nominal_amount)) {
            // Bersihkan format Rupiah jika ada (e.g., "Rp 1.500.000" -> 1500000)
            $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $this->nominal_amount));
            $this->merge([
                'nominal_amount' => $cleaned !== '' ? (float) $cleaned : null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pricing_scheme' => ['required', 'string', 'in:subsidy,commercial'],
            'nominal_amount' => ['required_if:pricing_scheme,commercial', 'nullable', 'numeric', 'min:0'],
            'payment_notes' => ['required_if:pricing_scheme,commercial', 'nullable', 'string', 'max:2000'],
            'approval_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'pricing_scheme.required' => 'Skema pembiayaan wajib dipilih.',
            'pricing_scheme.in' => 'Pilihan skema pembiayaan tidak valid.',
            'nominal_amount.required_if' => 'Nominal tarif wajib diisi untuk skema komersial/berbayar.',
            'nominal_amount.numeric' => 'Nominal tarif harus berupa angka yang valid.',
            'nominal_amount.min' => 'Nominal tarif tidak boleh bernilai negatif.',
            'payment_notes.required_if' => 'Nomor rekening kas daerah atau instruksi pembayaran wajib dicantumkan untuk skema komersial.',
            'payment_notes.max' => 'Instruksi pembayaran maksimal 2000 karakter.',
            'approval_notes.max' => 'Catatan kurator maksimal 2000 karakter.',
        ];
    }

    /**
     * Transform validated request payload into typed DTO.
     */
    public function toDTO(string $bookingId, string $marketingUserId): ApproveBookingData
    {
        $validated = $this->validated();
        $isSubsidy = ($validated['pricing_scheme'] ?? 'subsidy') === 'subsidy';

        return new ApproveBookingData(
            bookingId: $bookingId,
            pricingScheme: $validated['pricing_scheme'],
            nominalAmount: $isSubsidy ? 0.00 : (float) ($validated['nominal_amount'] ?? 0.00),
            paymentNotes: $isSubsidy ? null : ($validated['payment_notes'] ?? null),
            approvalNotes: $validated['approval_notes'] ?? null,
            marketingUserId: $marketingUserId,
        );
    }
}
