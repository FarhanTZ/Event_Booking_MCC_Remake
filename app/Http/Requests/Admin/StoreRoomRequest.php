<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\RoomData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role?->value === 'super_admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:rooms,slug'],
            'floor_level' => ['required', 'integer', 'between:1,8'],
            'capacity' => ['required', 'integer', 'min:1', 'max:5000'],
            'room_size_sqm' => ['required', 'numeric', 'min:1', 'max:10000'],
            'buffer_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'is_commercial_allowed' => ['sometimes', 'boolean'],
            'base_hourly_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['required', 'string', 'min:10'],
            'supported_layouts' => ['required', 'array', 'min:1'],
            'supported_layouts.*' => ['required', 'string', 'in:theater,classroom,u_shape,roundtable,hollow_square,boardroom,banquet,standing'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama ruangan wajib diisi.',
            'floor_level.required' => 'Tingkat lantai wajib dipilih.',
            'floor_level.between' => 'Tingkat lantai harus berada di antara Lantai 1 s/d 8.',
            'capacity.required' => 'Kapasitas maksimal ruangan wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1 orang.',
            'room_size_sqm.required' => 'Luas area ruangan (m2) wajib diisi.',
            'room_size_sqm.min' => 'Luas area minimal 1 m2.',
            'buffer_minutes.required' => 'Durasi buffer sterilisasi/pembersihan wajib diisi.',
            'base_hourly_rate.required' => 'Tarif dasar komersial per jam wajib diisi (isi 0 jika gratis).',
            'description.required' => 'Deskripsi ruangan wajib diisi.',
            'description.min' => 'Deskripsi ruangan minimal 10 karakter.',
            'supported_layouts.required' => 'Pilih minimal satu konfigurasi layout yang didukung.',
            'supported_layouts.min' => 'Pilih minimal satu konfigurasi layout yang didukung.',
            'photos.*.image' => 'File foto harus berupa gambar valid.',
            'photos.*.max' => 'Ukuran foto maksimal 5MB per file.',
        ];
    }

    public function toDTO(): RoomData
    {
        $slug = $this->filled('slug')
            ? Str::slug((string) $this->input('slug'))
            : Str::slug((string) $this->input('name'));

        return new RoomData(
            name: trim((string) $this->input('name')),
            slug: $slug,
            floorLevel: (int) $this->input('floor_level'),
            capacity: (int) $this->input('capacity'),
            roomSizeSqm: (float) $this->input('room_size_sqm'),
            bufferMinutes: (int) $this->input('buffer_minutes', 30),
            isCommercialAllowed: $this->boolean('is_commercial_allowed', true),
            baseHourlyRate: (float) $this->input('base_hourly_rate', 0),
            isActive: $this->boolean('is_active', true),
            description: trim((string) $this->input('description')),
            supportedLayouts: array_values((array) $this->input('supported_layouts', ['theater'])),
            photos: $this->file('photos', []) ?? []
        );
    }
}
