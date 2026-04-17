<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Upload;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ✅ TOTAL SALES
        $totalsales = Report::where('payment_status', 'paid')->sum('price');

        // ✅ LAST MONTH SALES
        $lastMonthSales = Report::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('price');

        // ✅ % CHANGE
        if ($lastMonthSales > 0) {
            $salesChange = (($totalsales - $lastMonthSales) / $lastMonthSales) * 100;
        } else {
            // 👉 if no previous data, treat as 100% growth
            $salesChange = $totalsales > 0 ? 100 : 0;
        }

        // ✅ ORDERS
        $totalOrders = Upload::count();

        // ✅ COMPLETE
        $totalcomplete = Report::where('payment_status', 'paid')->count();

        // ✅ PENDING
        $totalpending = Report::where('payment_status', 'pending')->count();

        // ✅ TABLE DATA (LATEST REPORTS)
        $reports = Report::with('upload.user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalsales',
            'salesChange',
            'totalOrders',
            'totalcomplete',
            'totalpending',
            'reports'
        ));
    }

    public function getOrder()
    {
        $reports = Report::with('upload.user')
    ->latest()
    ->paginate(1); // 10 per page

        return view('admin.order', compact('reports'));
    }
}
