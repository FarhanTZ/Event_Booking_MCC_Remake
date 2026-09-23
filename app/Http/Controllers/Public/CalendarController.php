<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $roomId = $request->query("room_id", 1);
        $date = $request->query("date", now()->toDateString());

        return view("public.calendar", compact("roomId", "date"));
    }
}
