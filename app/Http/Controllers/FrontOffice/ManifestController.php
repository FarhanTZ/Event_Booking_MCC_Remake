<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Queries\FrontOffice\GetDailyManifestQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManifestController extends Controller
{
    /**
     * Display the Front Office daily event manifest (FR-FO-01).
     *
     * @param Request $request
     * @param GetDailyManifestQuery $query
     * @return View
     */
    public function index(Request $request, GetDailyManifestQuery $query): View
    {
        // Mendukung tanggal hari ini (CURRENT_DATE) atau simulasi tanggal operasional
        $targetDateInput = $request->query('date');
        $targetDate = $targetDateInput ? Carbon::parse($targetDateInput) : Carbon::today();

        $metrics = $query->getMetrics($targetDate);
        $manifestSlots = $query->getDailyManifestSlots($request, $targetDate);
        $rooms = Room::query()->where('is_active', true)->orderBy('floor_level')->orderBy('name')->get();

        return view('front_office.manifest.index', [
            'metrics' => $metrics,
            'manifestSlots' => $manifestSlots,
            'rooms' => $rooms,
            'targetDate' => $targetDate,
            'currentStatus' => $request->query('status', 'all'),
            'currentSearch' => $request->query('q', ''),
            'currentFloor' => $request->query('floor_level', 'all'),
            'currentRoom' => $request->query('room_id', 'all'),
        ]);
    }
}
