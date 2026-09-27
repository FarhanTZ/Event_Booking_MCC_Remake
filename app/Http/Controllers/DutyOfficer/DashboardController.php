<?php

namespace App\Http\Controllers\DutyOfficer;

use App\Actions\DutyOfficer\GetDutyOfficerTasksAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DutyOfficer\DutyOfficerFilterRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the mobile-first Duty Officer Field Dashboard (FR-DO-01).
     */
    public function index(DutyOfficerFilterRequest $request, GetDutyOfficerTasksAction $action): View|JsonResponse
    {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId);
        $data = $action->execute($dto);

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('duty_officer.dashboard.index', $data);
    }
}
