<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        return view("public.catalog");
    }

    public function show(string $id)
    {
        return view("public.catalog", ["roomId" => $id]);
    }
}
