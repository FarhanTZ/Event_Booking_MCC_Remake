<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Queries\Marketing\GetMarketingDashboardQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Stream PDF proposal file securely with temporary signed URL for in-app browser preview (REQ-MKT-06).
     */
    public function streamProposal(Request $request, Booking $booking): Response
    {
        $eventDetail = $booking->eventDetail;
        if (! $eventDetail || ! $eventDetail->proposal_file_url) {
            abort(404, 'Berkas proposal tidak ditemukan.');
        }

        $rawPath = ltrim(str_replace('/storage/', '', $eventDetail->proposal_file_url), '/');
        
        if (! Storage::disk('public')->exists($rawPath)) {
            $fullPath = storage_path('app/public/' . $rawPath);
            if (! file_exists($fullPath)) {
                abort(404, 'File proposal tidak tersedia pada penyimpanan server.');
            }
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Proposal-' . $booking->booking_code . '.pdf"',
            ]);
        }

        $fullPath = Storage::disk('public')->path($rawPath);
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Proposal-' . $booking->booking_code . '.pdf"',
        ]);
    }

    /**
     * Stream banner/poster image file securely for in-app preview (REQ-MKT-06).
     */
    public function streamPoster(Request $request, Booking $booking): Response
    {
        $eventDetail = $booking->eventDetail;
        if (! $eventDetail || ! $eventDetail->poster_image_url) {
            abort(404, 'Poster banner tidak ditemukan.');
        }

        $rawPath = ltrim(str_replace('/storage/', '', $eventDetail->poster_image_url), '/');

        if (! Storage::disk('public')->exists($rawPath)) {
            $fullPath = storage_path('app/public/' . $rawPath);
            if (! file_exists($fullPath)) {
                abort(404, 'File poster tidak tersedia pada penyimpanan server.');
            }
            return response()->file($fullPath);
        }

        $fullPath = Storage::disk('public')->path($rawPath);
        return response()->file($fullPath);
    }
}
