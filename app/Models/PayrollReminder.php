<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollReminder extends Model
{
    protected $fillable = [
        'contractor_clab_no',
        'contractor_name',
        'contractor_email',
        'month',
        'year',
        'message',
        'sent_by',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
