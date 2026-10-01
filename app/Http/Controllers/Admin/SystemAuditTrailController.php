<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\GetSystemAuditTrailAction;
use App\DataTransferObjects\Admin\SystemAuditTrailFilterData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemAuditTrailController extends Controller
{
    /**
     * Display System-Wide Audit Trail Across All Checkpoints (FR-ADM-07).
     */
    public function index(Request $request, GetSystemAuditTrailAction $action): View
    {
        $dto = SystemAuditTrailFilterData::fromArray($request->all());
        $result = $action->execute($dto);

        return view('admin.audit.index', [
            'auditLogs' => $result['audit_logs'],
            'metrics' => $result['metrics'],
            'filter' => $result['filter'],
            'actors' => $result['actors'],
        ]);
    }
}
