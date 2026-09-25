<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the authenticated user's dashboard with live metrics and real booking requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Query real bookings belonging to the authenticated user
        $bookings = $user->bookings()
            ->with(['slots.room', 'eventDetail', 'approvals'])
            ->latest('created_at')
            ->get();

        $metrics = [
            'awaiting' => $bookings->filter(fn($b) => in_array($b->status instanceof \BackedEnum ? $b->status->value : $b->status, ['draft', 'awaiting_marketing']))->count(),
            'revised' => $bookings->filter(fn($b) => ($b->status instanceof \BackedEnum ? $b->status->value : $b->status) === 'revised')->count(),
            'confirmed' => $bookings->filter(fn($b) => in_array($b->status instanceof \BackedEnum ? $b->status->value : $b->status, ['confirmed', 'checked_in', 'completed']))->count(),
            'total' => $bookings->count(),
        ];

        return view('user.dashboard', [
            'user' => $user,
            'bookings' => $bookings,
            'metrics' => $metrics,
        ]);
    }
}
