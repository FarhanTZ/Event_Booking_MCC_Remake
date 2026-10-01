<?php

namespace App\DataTransferObjects\DutyOfficer;

use App\Http\Requests\DutyOfficer\DutyOfficerFilterRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

readonly class DutyOfficerDashboardFilterData
{
    public function __construct(
        public string $userId,
        public ?int $floor = null,
        public ?string $shift = 'all', // 'morning', 'evening', 'all'
        public ?string $date = null,   // Y-m-d
        public ?string $scope = 'assigned', // 'assigned' (Tugas Saya), 'all' (Semua Tugas Lantai)
        public ?string $status = 'all', // 'all', 'checked_in', 'upcoming', 'completed'
        public ?string $search = null
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $date = $request->input('date', $today);

        return new self(
            userId: $userId,
            floor: $request->filled('floor') ? (int) $request->input('floor') : null,
            shift: $request->input('shift', 'all') ?: 'all',
            date: $date,
            scope: $request->input('scope', 'assigned') ?: 'assigned',
            status: $request->input('status', 'all') ?: 'all',
            search: $request->filled('search') ? trim((string) $request->input('search')) : null
        );
    }
}
