<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\HandoverUnclaimedKtpData;
use Illuminate\Foundation\Http\FormRequest;

class HandoverUnclaimedKtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isFrontOffice() || $this->user()->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ktp_vault_log_id' => ['required', 'integer', 'exists:ktp_vault_logs,id'],
            'retrieved_by_name' => ['required', 'string', 'max:150'],
            'retrieved_by_nik' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
            'unsuspend_user' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'ktp_vault_log_id.required' => 'Identitas log KTP wajib disertakan.',
            'ktp_vault_log_id.exists' => 'Data log KTP tidak ditemukan dalam brankas inventaris.',
            'retrieved_by_name.required' => 'Nama penerima / pengambil fisik KTP wajib diisi.',
            'retrieved_by_name.max' => 'Nama penerima maksimal 150 karakter.',
            'retrieved_by_nik.max' => 'NIK penerima maksimal 30 karakter.',
            'notes.max' => 'Catatan serah terima maksimal 500 karakter.',
        ];
    }

    /**
     * Transform validated request data to strongly typed DTO.
     */
    public function toDTO(string $userId): HandoverUnclaimedKtpData
    {
        return new HandoverUnclaimedKtpData(
            ktpVaultLogId: (int) $this->input('ktp_vault_log_id'),
            retrievedByName: trim((string) $this->input('retrieved_by_name')),
            retrievedByNik: $this->filled('retrieved_by_nik') ? trim((string) $this->input('retrieved_by_nik')) : null,
            notes: $this->filled('notes') ? trim((string) $this->input('notes')) : null,
            unsuspendUser: $this->boolean('unsuspend_user'),
            handledByFoId: $userId
        );
    }
}
