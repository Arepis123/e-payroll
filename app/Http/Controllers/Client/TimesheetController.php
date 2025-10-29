<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    /**
     * View specific payroll submission details
     */
    public function show(Request $request, $id)
    {
        $clabNo = $request->user()->contractor_clab_no;

        $submission = \App\Models\PayrollSubmission::with(['workers.transactions', 'payment'])
            ->where('id', $id)
            ->where('contractor_clab_no', $clabNo)
            ->firstOrFail();

        // Load previous month's OT data
        $currentMonth = $submission->month;
        $currentYear = $submission->year;

        $previousMonth = $currentMonth - 1;
        $previousYear = $currentYear;

        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear = $currentYear - 1;
        }

        // Find previous month's submission for the same contractor
        $previousSubmission = \App\Models\PayrollSubmission::with(['workers'])
            ->where('contractor_clab_no', $clabNo)
            ->where('month', $previousMonth)
            ->where('year', $previousYear)
            ->first();

        $previousOtStats = [];
        if ($previousSubmission) {
            $previousWorkers = $previousSubmission->workers;
            $previousOtStats = [
                'total_ot_hours' => $previousWorkers->sum(function ($worker) {
                    return $worker->ot_normal_hours + $worker->ot_rest_hours + $worker->ot_public_hours;
                }),
                'total_ot_pay' => $previousWorkers->sum('total_ot_pay'),
                'total_weekday_ot_hours' => $previousWorkers->sum('ot_normal_hours'),
                'total_weekday_ot_pay' => $previousWorkers->sum('ot_normal_pay'),
                'total_rest_ot_hours' => $previousWorkers->sum('ot_rest_hours'),
                'total_rest_ot_pay' => $previousWorkers->sum('ot_rest_pay'),
                'total_public_ot_hours' => $previousWorkers->sum('ot_public_hours'),
                'total_public_ot_pay' => $previousWorkers->sum('ot_public_pay'),
            ];
        } else {
            $previousOtStats = [
                'total_ot_hours' => 0,
                'total_ot_pay' => 0,
                'total_weekday_ot_hours' => 0,
                'total_weekday_ot_pay' => 0,
                'total_rest_ot_hours' => 0,
                'total_rest_ot_pay' => 0,
                'total_public_ot_hours' => 0,
                'total_public_ot_pay' => 0,
            ];
        }

        return view('client.timesheet-detail', compact('submission', 'previousSubmission', 'previousOtStats'));
    }
}
