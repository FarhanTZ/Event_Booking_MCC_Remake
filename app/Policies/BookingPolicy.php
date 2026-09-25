<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view the booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        // 1. Staff internal MCC can inspect bookings
        if ($user->isMarketing() || $user->isFrontOffice() || $user->isDutyOfficer() || $user->isSuperAdmin()) {
            return true;
        }

        // 2. Public applicant can ONLY inspect their own bookings (Anti-IDOR)
        return $booking->user_id === $user->id;
    }

    /**
     * Determine whether the user can revise or update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        if ($user->isSuperAdmin() || $user->isMarketing()) {
            return true;
        }

        // Applicant can only update if it is their booking AND in revised/draft status
        $statusVal = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;
        return $booking->user_id === $user->id && in_array($statusVal, ['draft', 'revised'], true);
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Cannot cancel already completed or checked-in bookings
        $statusVal = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;
        return $booking->user_id === $user->id && ! in_array($statusVal, ['checked_in', 'completed', 'cancelled'], true);
    }
}
