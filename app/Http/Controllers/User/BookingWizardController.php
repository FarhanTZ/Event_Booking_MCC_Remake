<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingWizardController extends Controller
{
    public function index(Request $request)
    {
        return view("user.wizard");
    }

    public function store(Request $request)
    {
        return redirect("/dashboard")->with("success", "Permohonan reservasi berhasil diajukan! Menunggu kurasi tim MCC.");
    }
}
