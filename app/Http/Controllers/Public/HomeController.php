<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman utama pengenalan Malang Creative Center (MCC)
     */
    public function index(Request $request)
    {
        return view("public.home");
    }
}
