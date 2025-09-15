<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with all incident reports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all incident reports and order them by creation date
        $reports = IncidentReport::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard.index', compact('reports'));
    }
}
