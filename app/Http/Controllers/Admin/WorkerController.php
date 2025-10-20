<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function show($workerId)
    {
        // Get worker from the worker_db database with all relationships
        $worker = Worker::with(['country', 'workTrade', 'contracts'])
            ->where('wkr_id', $workerId)
            ->firstOrFail();

        // Get the active contract
        $contract = $worker->activeContract;

        return view('admin.workers.show', compact('worker', 'contract'));
    }
}
