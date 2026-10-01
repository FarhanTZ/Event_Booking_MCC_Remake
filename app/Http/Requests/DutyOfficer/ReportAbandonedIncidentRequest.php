<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ReportAbandonedIncidentData;
use App\Enums\RoomIncidentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReportAbandonedIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role?->value, ['duty_officer', 'super_admin']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', new Enum(RoomIncidentCategory::class)],
            'item_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB per photo
            ],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'Kategori insiden wajib dipilih.',
            'item_name.required' => 'Nama barang/fasilitas terdampak wajib diisi.',
            'description.required' => 'Deskripsi kronologis temuan lapangan wajib diisi.',
            'description.min' => 'Deskripsi kronologis minimal 5 karakter.',
            'photos.required' => 'Wajib melampirkan minimal 1 foto bukti kamera lapangan.',
            'photos.min' => 'Wajib melampirkan minimal 1 foto bukti kamera lapangan.',
            'photos.max' => 'Maksimal 5 foto bukti dapat diunggah sekaligus.',
            'photos.*.image' => 'File bukti harus berupa file gambar valid.',
            'photos.*.mimes' => 'Format foto harus berupa JPG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran setiap foto tidak boleh melebihi 5MB.',
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): ReportAbandonedIncidentData
    {
        return new ReportAbandonedIncidentData(
            dutyOfficerId: $dutyOfficerId,
            bookingId: $bookingId,
            category: RoomIncidentCategory::from($this->input('category')),
            itemName: trim((string) $this->input('item_name')),
            description: trim((string) $this->input('description')),
            photos: $this->file('photos') ?? [],
            slotId: $this->filled('slot_id') ? (int) $this->input('slot_id') : null
        );
    }
}
