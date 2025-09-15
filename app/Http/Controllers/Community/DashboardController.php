<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the community dashboard with reports submitted by the logged-in user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch only the incident reports submitted by the current user
        $reports = IncidentReport::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->get();
        return view('community.dashboard.index', compact('reports'));
    }
}
