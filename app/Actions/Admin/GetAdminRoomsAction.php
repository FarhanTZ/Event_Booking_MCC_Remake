<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\RoomFilterData;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;

class GetAdminRoomsAction
{
    /**
     * Get filtered rooms list and inventory KPI statistics (FR-ADM-01).
     *
     * @param RoomFilterData $dto
     * @return array{
     *   rooms: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *   metrics: array{total: int, active: int, inactive: int, commercial: int, total_capacity: int},
     *   filter: RoomFilterData
     * }
     */
    public function execute(RoomFilterData $dto): array
    {
        $baseQuery = Room::query()
            ->with(['facilities', 'images', 'primaryImage'])
            ->withCount(['facilities', 'bookingSlots']);

        // Overall metrics query (unfiltered by search/floor)
        $metrics = [
            'total' => Room::count(),
            'active' => Room::where('is_active', true)->count(),
            'inactive' => Room::where('is_active', false)->count(),
            'commercial' => Room::where('is_commercial_allowed', true)->count(),
            'total_capacity' => (int) Room::where('is_active', true)->sum('capacity'),
            'total_facilities' => \App\Models\RoomFacility::count(),
        ];

        // 1. Search filter
        if (! empty($dto->search)) {
            $search = '%' . strtolower($dto->search) . '%';
            $baseQuery->where(function (Builder $query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(slug) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$search])
                    ->orWhereHas('facilities', function (Builder $sq) use ($search) {
                        $sq->whereRaw('LOWER(facility_name) LIKE ?', [$search]);
                    });
            });
        }

        // 2. Floor filter
        if ($dto->floor !== null) {
            $baseQuery->where('floor_level', $dto->floor);
        }

        // 3. Status filter
        if ($dto->status === 'active') {
            $baseQuery->where('is_active', true);
        } elseif ($dto->status === 'inactive') {
            $baseQuery->where('is_active', false);
        } elseif ($dto->status === 'commercial') {
            $baseQuery->where('is_commercial_allowed', true);
        }

        // 4. Sorting
        $allowedSorts = ['name', 'floor_level', 'capacity', 'base_hourly_rate', 'created_at', 'room_size_sqm'];
        $sortField = in_array($dto->sortField, $allowedSorts) ? $dto->sortField : 'floor_level';
        $sortDirection = strtolower($dto->sortDirection) === 'desc' ? 'desc' : 'asc';

        $rooms = $baseQuery->orderBy($sortField, $sortDirection)
            ->orderBy('id', 'asc')
            ->paginate(12)
            ->withQueryString();

        return [
            'rooms' => $rooms,
            'metrics' => $metrics,
            'filter' => $dto,
        ];
    }
}
