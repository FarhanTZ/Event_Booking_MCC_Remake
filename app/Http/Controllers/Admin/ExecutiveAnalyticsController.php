<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\GetExecutiveAnalyticsAction;
use App\DataTransferObjects\Admin\ExecutiveAnalyticsFilterData;
use App\Http\Controllers\Controller;
use App\Services\Admin\ExecutiveReportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExecutiveAnalyticsController extends Controller
{
    /**
     * Display Executive Dashboard & Building Utilization Analytics (FR-ADM-06).
     */
    public function index(Request $request, GetExecutiveAnalyticsAction $action): View
    {
        $dto = ExecutiveAnalyticsFilterData::fromArray($request->all());
        $analytics = $action->execute($dto);

        return view('admin.analytics.index', [
            'analytics' => $analytics,
            'kpis' => $analytics['kpis'],
            'floorOccupancy' => $analytics['floor_occupancy'],
            'roomOccupancy' => $analytics['room_occupancy'],
            'ekrafDistribution' => $analytics['ekraf_distribution'],
            'pricingScheme' => $analytics['pricing_scheme'],
            'incidents' => $analytics['incidents'],
            'roomsList' => $analytics['rooms_list'],
            'filter' => $analytics['filter'],
            'ekrafSubsectors' => GetExecutiveAnalyticsAction::EKRAF_SUBSECTORS,
        ]);
    }

    /**
     * Export executive report to PDF.
     */
    public function exportPdf(Request $request, ExecutiveReportExportService $exportService): Response
    {
        $dto = ExecutiveAnalyticsFilterData::fromArray($request->all());
        return $exportService->exportPdf($dto);
    }

    /**
     * Export executive report to CSV/Excel.
     */
    public function exportCsv(Request $request, ExecutiveReportExportService $exportService): StreamedResponse
    {
        $dto = ExecutiveAnalyticsFilterData::fromArray($request->all());
        return $exportService->exportCsv($dto);
    }
}
