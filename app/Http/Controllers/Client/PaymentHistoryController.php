<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PayrollPayment;
use App\Models\PayrollSubmission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    /**
     * Display payment history
     */
    public function index(Request $request)
    {
        $clabNo = $request->user()->contractor_clab_no;

        if (!$clabNo) {
            return view('client.payments', [
                'error' => 'No contractor CLAB number assigned to your account.',
            ]);
        }

        // Get year filter (default to current year)
        $selectedYear = $request->input('year', now()->year);

        // Get all payments for this contractor with submission details
        $payments = PayrollPayment::whereHas('submission', function ($query) use ($clabNo, $selectedYear) {
            $query->where('contractor_clab_no', $clabNo)
                  ->whereYear('created_at', $selectedYear);
        })
        ->with(['submission'])
        ->orderBy('completed_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Calculate statistics
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // This month (pending + paid in current month)
        $thisMonthAmount = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('total_with_penalty');

        $thisMonthStatus = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->value('status');

        // Last month (paid)
        $lastMonth = now()->subMonth();
        $lastMonthAmount = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $lastMonth->month)
            ->where('year', $lastMonth->year)
            ->where('status', 'paid')
            ->sum('total_with_penalty');

        // This year total
        $thisYearAmount = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('year', $currentYear)
            ->where('status', 'paid')
            ->sum('total_with_penalty');

        $thisYearCount = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('year', $currentYear)
            ->where('status', 'paid')
            ->count();

        // Average monthly
        $avgMonthly = $thisYearCount > 0 ? $thisYearAmount / $thisYearCount : 0;

        // Available years for filter
        $availableYears = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $stats = [
            'this_month_amount' => $thisMonthAmount,
            'this_month_status' => $thisMonthStatus,
            'last_month_amount' => $lastMonthAmount,
            'this_year_amount' => $thisYearAmount,
            'this_year_count' => $thisYearCount,
            'avg_monthly' => $avgMonthly,
        ];

        return view('client.payments', compact(
            'payments',
            'stats',
            'selectedYear',
            'availableYears'
        ));
    }
}
