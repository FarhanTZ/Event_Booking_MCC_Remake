<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateStaffAction;
use App\Actions\Admin\DeleteStaffAction;
use App\Actions\Admin\GetAdminStaffListAction;
use App\Actions\Admin\ResetStaffPasswordAction;
use App\Actions\Admin\ToggleStaffStatusAction;
use App\Actions\Admin\UpdateStaffAction;
use App\DataTransferObjects\Admin\StaffFilterData;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetStaffPasswordRequest;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\ToggleStaffStatusRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffManagementController extends Controller
{
    /**
     * Display a listing of internal staff accounts (FR-ADM-03).
     */
    public function index(Request $request, GetAdminStaffListAction $action): View
    {
        $dto = StaffFilterData::fromArray($request->all());
        $data = $action->execute($dto);

        return view('admin.staff.index', [
            'staffs' => $data['staffs'],
            'metrics' => $data['metrics'],
            'filter' => $data['filter'],
        ]);
    }

    /**
     * Show the form for creating a new staff account.
     */
    public function create(): View
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created staff account in storage.
     */
    public function store(StoreStaffRequest $request, CreateStaffAction $action): RedirectResponse
    {
        $staff = $action->execute($request->toDto());

        return redirect()->route('admin.staff.index')
            ->with('success', "Akun staf '{$staff->full_name}' dengan role {$staff->role->label()} berhasil dibuat.");
    }

    /**
     * Show the form for editing the specified staff account.
     */
    public function edit(User $staff): View
    {
        // Protect from editing non-staff public users via this admin portal
        if ($staff->role === UserRole::USER) {
            abort(404);
        }

        return view('admin.staff.edit', [
            'staff' => $staff,
        ]);
    }

    /**
     * Update the specified staff account in storage.
     */
    public function update(UpdateStaffRequest $request, User $staff, UpdateStaffAction $action): RedirectResponse
    {
        $updatedStaff = $action->execute($staff, $request->toDto());

        return redirect()->route('admin.staff.index')
            ->with('success', "Data akun staf '{$updatedStaff->full_name}' berhasil diperbarui.");
    }

    /**
     * Reset password for the specified staff account.
     */
    public function resetPassword(ResetStaffPasswordRequest $request, User $staff, ResetStaffPasswordAction $action): RedirectResponse
    {
        $action->execute($staff, $request->validated('password'));

        return redirect()->route('admin.staff.index')
            ->with('success', "Kata sandi akun staf '{$staff->full_name}' berhasil direset.");
    }

    /**
     * Toggle suspended / active status for the specified staff account.
     */
    public function toggleStatus(ToggleStaffStatusRequest $request, User $staff, ToggleStaffStatusAction $action): RedirectResponse
    {
        // Prevent self-deactivation by the currently authenticated Super Admin
        if ($staff->id === auth()->id()) {
            return redirect()->route('admin.staff.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $toggledStaff = $action->execute($staff, $request->validated('suspension_reason'));
        $statusLabel = $toggledStaff->is_suspended ? 'dinonaktifkan (mutasi/berhenti)' : 'diaktifkan kembali';

        return redirect()->route('admin.staff.index')
            ->with('success', "Akun staf '{$toggledStaff->full_name}' berhasil {$statusLabel}.");
    }

    /**
     * Remove the specified staff account from storage.
     */
    public function destroy(User $staff, DeleteStaffAction $action): RedirectResponse
    {
        // Prevent self-deletion
        if ($staff->id === auth()->id()) {
            return redirect()->route('admin.staff.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staffName = $staff->full_name;
        $action->execute($staff);

        return redirect()->route('admin.staff.index')
            ->with('success', "Akun staf '{$staffName}' berhasil dihapus dari sistem.");
    }
}
