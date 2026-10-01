<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ReportRoomIncidentData;
use App\Enums\RoomIncidentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReportRoomIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'category' => ['required', new Enum(RoomIncidentCategory::class)],
            'item_name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Kategori insiden wajib dipilih.',
            'item_name.required' => 'Nama barang atau fasilitas terdampak wajib diisi.',
            'description.required' => 'Deskripsi kronologis temuan insiden di lapangan wajib diisi.',
            'description.min' => 'Deskripsi kronologis minimal 5 karakter.',
            'photos.required' => 'Foto bukti kamera lapangan wajib diunggah.',
            'photos.min' => 'Minimal sertakan 1 foto bukti kondisi di lapangan.',
            'photos.*.image' => 'File bukti wajib berupa foto gambar asli.',
            'photos.*.mimes' => 'Format foto yang diizinkan hanya JPG, JPEG, atau PNG.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 10MB.',
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): ReportRoomIncidentData
    {
        return ReportRoomIncidentData::fromRequest($this, $dutyOfficerId, $bookingId);
    }
}
