<?php

namespace App\Http\Requests\Booking;

use App\DataTransferObjects\Booking\CreateBookingProposalData;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $validEventCategories = [
            'Dinas',
            'Kampus',
            'Komersial',
            'Komunitas',
            'Lainnya',
        ];

        $validEkrafSubsectors = [
            'Aplikasi',
            'Arsitektur',
            'Desain Interior',
            'DKV',
            'Fashion',
            'Film',
            'Fotografi',
            'Game',
            'Konten Digital',
            'Kriya',
            'Kuliner',
            'Modifikasi Otomotif',
            'Musik',
            'Penerbitan',
            'Periklanan',
            'Pertunjukan',
            'Produk',
            'Seni Rupa',
            'Sulih Suara',
            'Teknologi Baru',
            'Televisi & Radio',
        ];

        $validEventTypes = [
            'Terbatas',
            'Terbuka Untuk Umum',
            'Internal',
        ];

        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'slots' => ['required', 'array', 'min:1'],
            'slots.*' => ['required', 'string'],
            'pic_name' => ['required', 'string', 'max:150'],
            'pic_phone' => ['required', 'string', 'max:25'],
            'pic_emergency_phone' => ['nullable', 'string', 'max:25'],
            'official_letter_number' => ['nullable', 'string', 'max:100'],
            'event_name' => ['required', 'string', 'max:200'],
            'event_category' => ['required', 'string', 'in:' . implode(',', $validEventCategories)],
            'ekraf_subsector' => ['required', 'string', 'in:' . implode(',', $validEkrafSubsectors)],
            'event_type' => ['required', 'string', 'in:' . implode(',', $validEventTypes)],
            'estimated_participants' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $room = Room::find($this->input('room_id'));
                    if ($room && $value > $room->capacity) {
                        $fail("Estimasi jumlah peserta ({$value} orang) wajib ≤ kapasitas maksimal ruangan ({$room->capacity} orang).");
                    }
                },
            ],
            'event_description' => ['required', 'string', 'min:5'],
            'equipment_needed' => ['nullable', 'string', 'max:2000'],
            // Proposal: format .pdf, maksimal 2 MB (2048 KB)
            'proposal_file' => ['required', 'file', 'mimes:pdf', 'max:2048'],
            // Poster: format .jpg, .jpeg, .png, maksimal 2 MB (2048 KB)
            'poster_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'tos_agreed' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'proposal_file.required' => 'Proposal event wajib diunggah.',
            'proposal_file.mimes' => 'Format file proposal harus berekstensi .pdf.',
            'proposal_file.max' => 'Ukuran file proposal maksimal adalah 2 MB.',
            'poster_file.mimes' => 'Format file banner/poster harus berekstensi .jpg, .jpeg, atau .png.',
            'poster_file.max' => 'Ukuran file banner/poster maksimal adalah 2 MB.',
            'tos_agreed.accepted' => 'Anda wajib menyetujui syarat dan ketentuan pemakaian gedung MCC serta bersedia menyerahkan fisik KTP asli penanggung jawab acara di meja Front Office saat registrasi hari-H.',
        ];
    }

    public function toDto(): CreateBookingProposalData
    {
        $user = $this->user();

        return new CreateBookingProposalData(
            roomId: (int) $this->validated('room_id'),
            date: (string) $this->validated('date'),
            slots: (array) $this->validated('slots'),
            userId: (string) $user->id,
            eventName: (string) $this->validated('event_name'),
            eventCategory: (string) $this->validated('event_category'),
            ekrafSubsector: (string) $this->validated('ekraf_subsector'),
            eventType: (string) $this->validated('event_type'),
            eventDescription: (string) $this->validated('event_description'),
            equipmentNeeded: $this->validated('equipment_needed'),
            requestedFacilities: [],
            estimatedParticipants: (int) $this->validated('estimated_participants'),
            picName: (string) $this->validated('pic_name'),
            picPhone: (string) $this->validated('pic_phone'),
            picEmergencyPhone: $this->validated('pic_emergency_phone'),
            institutionName: (string) ($user->institution_name ?? $this->validated('pic_name')),
            officialLetterNumber: $this->validated('official_letter_number'),
            proposalFile: $this->file('proposal_file'),
            posterFile: $this->file('poster_file'),
            tosAgreed: (bool) $this->validated('tos_agreed')
        );
    }
}
