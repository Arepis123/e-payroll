<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    /**
     * The connection name for the model.
     * This points to the second database (worker_db)
     */
    protected $connection = 'worker_db';

    /**
     * The table associated with the model.
     */
    protected $table = 'workers';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'wkr_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'wkr_passno',
        'wkr_name',
        'wkr_dob',
        'wkr_wtrade',
        'wkr_salary',
        'wkr_status',
        'wkr_tel',
        'wkr_address1',
        'wkr_address2',
        'wkr_address3',
        'wkr_pcode',
        'wkr_state',
        'wkr_currentemp',
        'wkr_nationality',
        'wkr_gender',
        'wkr_passexp',
        'wkr_permitexp',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'wkr_salary' => 'decimal:2',
        'wkr_dob' => 'date',
        'wkr_passexp' => 'date',
        'wkr_permitexp' => 'date',
        'wkr_contractexp' => 'date',
        'wkr_entrydate' => 'date',
        'wkr_createddate' => 'datetime',
        'wkr_modifieddate' => 'datetime',
    ];

    /**
     * Get the cache key for this worker
     */
    public function getCacheKey(): string
    {
        return "worker:{$this->wkr_id}";
    }

    /**
     * Scope a query to only include active workers.
     * Status: 1 = Active, 2 = Inactive, etc.
     */
    public function scopeActive($query)
    {
        return $query->where('wkr_status', '2');
    }

    /**
     * Scope a query to only include workers by position/trade.
     */
    public function scopePosition($query, string $position)
    {
        return $query->where('wkr_wtrade', 'LIKE', "%{$position}%");
    }

    /**
     * Get the contractor that owns this worker
     */
    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'wkr_currentemp', 'ctr_clab_no');
    }

    /**
     * Accessor for name
     */
    public function getNameAttribute()
    {
        return $this->wkr_name;
    }

    /**
     * Accessor for IC number (passport number)
     */
    public function getIcNumberAttribute()
    {
        return $this->wkr_passno;
    }

    /**
     * Accessor for position
     */
    public function getPositionAttribute()
    {
        return $this->wkr_wtrade;
    }

    /**
     * Accessor for basic salary
     */
    public function getBasicSalaryAttribute()
    {
        return $this->wkr_salary;
    }

    /**
     * Accessor for status
     */
    public function getStatusAttribute()
    {
        return $this->wkr_status == '2' ? 'active' : 'inactive';
    }

    /**
     * Accessor for phone
     */
    public function getPhoneAttribute()
    {
        return $this->wkr_tel;
    }

    /**
     * Accessor for contractor ID
     */
    public function getContractorIdAttribute()
    {
        return $this->wkr_currentemp;
    }
}
