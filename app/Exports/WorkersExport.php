<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WorkersExport implements WithMultipleSheets
{
    protected $workers;

    public function __construct($workers)
    {
        $this->workers = $workers;
    }

    public function sheets(): array
    {
        return [
            new WorkerDetailsSheet($this->workers),
            new PayrollDetailsSheet($this->workers),
        ];
    }
}
