<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ApplyUserSanctionAction;
use App\Actions\Admin\GetSanctionReviewQueueAction;
use App\Actions\Admin\LiftUserSanctionAction;
use App\DataTransferObjects\Admin\SanctionFilterData;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApplySanctionRequest;
use App\Http\Requests\Admin\LiftSanctionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SanctionManagementController extends Controller
{
    /**
     * Display review queue for sanctions and blacklist enforcement (FR-ADM-04).
     */
    public function index(Request $request, GetSanctionReviewQueueAction $action): View
    {
        $dto = SanctionFilterData::fromArray($request->all());
        $data = $action->execute($dto);

        return view('admin.sanctions.index', [
            'applicants' => $data['applicants'],
            'metrics' => $data['metrics'],
            'filter' => $data['filter'],
        ]);
    }

    /**
     * Suspend / Blacklist applicant account (FR-ADM-04).
     */
    public function suspend(
        ApplySanctionRequest $request,
        User $user,
        ApplyUserSanctionAction $action
    ): RedirectResponse {
        // Prevent suspending internal staff via applicant sanctions portal
        if ($user->role !== UserRole::USER) {
            return redirect()->route('admin.sanctions.index')
                ->with('error', 'Hanya akun pemohon publik yang dapat dikenakan sanksi blacklist pemohon.');
        }

        $dto = $request->toDto((string) $user->id);
        $suspendedUser = $action->execute($user, $dto);

        return redirect()->route('admin.sanctions.index')
            ->with('success', "Akun pemohon '{$suspendedUser->full_name}' berhasil dibekukan (Blacklist). Akses pemesanan jadwal kalender diblokir otomatis.");
    }

    /**
     * Lift suspension and restore applicant account access (FR-ADM-04).
     */
    public function lift(
        LiftSanctionRequest $request,
        User $user,
        LiftUserSanctionAction $action
    ): RedirectResponse {
        if ($user->role !== UserRole::USER) {
            return redirect()->route('admin.sanctions.index')
                ->with('error', 'Aksi pemulihan sanksi hanya berlaku untuk akun pemohon publik.');
        }

        $restoredUser = $action->execute($user, $request->input('settlement_notes'));

        return redirect()->route('admin.sanctions.index')
            ->with('success', "Sanksi akun pemohon '{$restoredUser->full_name}' telah dicabut. Akses pemesanan jadwal kalender telah dipulihkan.");
    }
}
