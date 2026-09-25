<?php

namespace App\Http\Requests\Booking;

use App\DataTransferObjects\Booking\SubmitBookingRevisionData;
use Illuminate\Foundation\Http\FormRequest;

class SubmitBookingRevisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        return $this->user() && $this->user()->can('update', $booking);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pic_name' => ['required', 'string', 'max:255'],
            'pic_phone' => ['required', 'string', 'max:50'],
            'pic_emergency_phone' => ['nullable', 'string', 'max:50'],
            'official_letter_number' => ['nullable', 'string', 'max:100'],
            'event_name' => ['required', 'string', 'max:200'],
            'event_category' => ['required', 'string'],
            'ekraf_subsector' => ['required', 'string'],
            'event_type' => ['required', 'string'],
            'estimated_participants' => ['required', 'integer', 'min:1'],
            'event_description' => ['required', 'string'],
            'equipment_needed' => ['nullable', 'string'],
            'proposal_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'poster_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'tos_agreed' => ['accepted'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'pic_name.required' => 'Nama PIC penanggung jawab wajib diisi.',
            'pic_phone.required' => 'Nomor telepon PIC wajib diisi.',
            'event_name.required' => 'Nama kegiatan / event wajib diisi.',
            'event_category.required' => 'Kategori kegiatan wajib dipilih.',
            'ekraf_subsector.required' => 'Sub-sektor ekonomi kreatif wajib dipilih.',
            'event_type.required' => 'Sifat keterbukaan acara wajib dipilih.',
            'estimated_participants.required' => 'Estimasi jumlah peserta wajib diisi.',
            'estimated_participants.min' => 'Estimasi peserta minimal 1 orang.',
            'event_description.required' => 'Deskripsi kegiatan wajib diisi.',
            'proposal_file.mimes' => 'Format berkas proposal perbaikan harus berekstensi .pdf.',
            'proposal_file.max' => 'Ukuran berkas proposal perbaikan maksimal adalah 5 MB.',
            'poster_file.mimes' => 'Format poster harus berupa gambar (.jpg, .jpeg, .png).',
            'poster_file.max' => 'Ukuran file poster maksimal adalah 2 MB.',
            'notes.max' => 'Catatan revisi maksimal 1000 karakter.',
            'tos_agreed.accepted' => 'Anda wajib menyetujui syarat, ketentuan, dan pakta integritas pemakaian gedung MCC.',
        ];
    }

    /**
     * Transform request payload into typed DTO.
     */
    public function toDTO(string $bookingId, string $userId): SubmitBookingRevisionData
    {
        return new SubmitBookingRevisionData(
            bookingId: $bookingId,
            userId: $userId,
            picName: (string) $this->input('pic_name'),
            picPhone: (string) $this->input('pic_phone'),
            picEmergencyPhone: $this->input('pic_emergency_phone'),
            officialLetterNumber: $this->input('official_letter_number'),
            eventName: (string) $this->input('event_name'),
            eventCategory: (string) $this->input('event_category'),
            ekrafSubsector: (string) $this->input('ekraf_subsector'),
            eventType: (string) $this->input('event_type', 'Terbuka Untuk Umum'),
            estimatedParticipants: (int) $this->input('estimated_participants', 1),
            eventDescription: (string) $this->input('event_description', ''),
            equipmentNeeded: $this->input('equipment_needed'),
            proposalFile: $this->file('proposal_file'),
            posterFile: $this->file('poster_file'),
            revisionNotes: $this->input('notes'),
        );
    }
}
