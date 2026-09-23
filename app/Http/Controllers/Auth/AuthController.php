<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($request->filled("redirect_slot")) {
            return redirect("/booking/wizard?" . $request->input("redirect_slot"))->with("success", "Berhasil masuk! Slot jadwal Anda siap diisi.");
        }

        return redirect("/dashboard")->with("success", "Selamat datang di Portal Pemohon MCC.");
    }

    public function showRegister(Request $request)
    {
        return view("auth.register");
    }

    public function register(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "phone" => "required|string|max:20",
            "email" => "required|string|email|max:255|unique:users",
            "institution_name" => "required|string|max:255",
            "institution_type" => "required|string",
            "password" => "required|string|min:8|confirmed",
            "terms" => "accepted",
        ]);

        return redirect("/dashboard")->with("success", "Registrasi berhasil! Akun Anda siap digunakan.");
    }

    public function logout(Request $request)
    {
        return redirect("/")->with("success", "Anda telah berhasil keluar.");
    }
}
