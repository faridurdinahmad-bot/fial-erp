<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Display the ERP dashboard.
     */
    public function index()
    {
        return view('dashboard.index');
    }
}
