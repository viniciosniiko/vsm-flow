<?php

namespace App\Http\Controllers;

use App\Models\Flow;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'flows' => Flow::latest()->take(8)->get(),
            'totalFlows' => Flow::count(),
            'draftFlows' => Flow::where('status', 'draft')->count(),
            'approvedFlows' => Flow::where('status', 'approved')->count(),
        ]);
    }
}
