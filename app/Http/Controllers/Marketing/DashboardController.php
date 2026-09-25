<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Queries\Marketing\GetMarketingDashboardQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the marketing triage and curation dashboard.
     */
    public function index(Request $request, GetMarketingDashboardQuery $query): View
    {
        $metrics = $query->getMetrics();
        $curations = $query->getCurations($request);
        $ekrafDistribution = $query->getEkrafDistribution();

        return view('marketing.dashboard', [
            'metrics' => $metrics,
            'curations' => $curations,
            'ekrafDistribution' => $ekrafDistribution,
            'currentStatus' => $request->query('status', 'all'),
            'currentSearch' => $request->query('q', ''),
            'currentCategory' => $request->query('category', 'all'),
            'currentSubSector' => $request->query('sub_sector', 'all'),
            'currentFloor' => $request->query('floor_level', 'all'),
            'currentDateFrom' => $request->query('date_from', ''),
            'currentDateTo' => $request->query('date_to', ''),
        ]);
    }
}
