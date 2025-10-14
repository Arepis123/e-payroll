<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contractor extends Model
{
    /**
     * The connection name for the model.
     * This points to the second database (worker_db)
     */
    protected $connection = 'worker_db';

    /**
     * The table associated with the model.
     */
    protected $table = 'contractors';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'ctr_clab_no';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ctr_clab_no',
        'ctr_comp_name',
        'ctr_comp_regno',
        'ctr_contact_name',
        'ctr_contact_mobileno',
        'ctr_telno',
        'ctr_email',
        'ctr_addr1',
        'ctr_addr2',
        'ctr_addr3',
        'ctr_pcode',
        'ctr_state',
        'ctr_status',
        'ctr_cidb_regno',
        'ctr_grade',
        'ctr_datereg',
        'ctr_clabexp_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'ctr_datereg' => 'date',
        'ctr_validdate' => 'date',
        'ctr_clabexp_date' => 'date',
        'ctr_cidbexp_date' => 'date',
    ];

    /**
     * Get the cache key for this contractor
     */
    public function getCacheKey(): string
    {
        return "contractor:{$this->ctr_clab_no}";
    }

    /**
     * Scope a query to only include active contractors.
     * Status: 1 = Active, 2 = Inactive
     */
    public function scopeActive($query)
    {
        return $query->where('ctr_status', '2');
    }

    /**
     * Get the workers associated with this contractor
     */
    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class, 'wkr_currentemp', 'ctr_clab_no');
    }

    /**
     * Accessor for company name
     */
    public function getCompanyNameAttribute()
    {
        return $this->ctr_comp_name;
    }

    /**
     * Accessor for registration number
     */
    public function getRegistrationNumberAttribute()
    {
        return $this->ctr_comp_regno;
    }

    /**
     * Accessor for contact person
     */
    public function getContactPersonAttribute()
    {
        return $this->ctr_contact_name;
    }

    /**
     * Accessor for phone
     */
    public function getPhoneAttribute()
    {
        return $this->ctr_contact_mobileno ?? $this->ctr_telno;
    }

    /**
     * Accessor for email
     */
    public function getEmailAttribute()
    {
        return $this->ctr_email;
    }

    /**
     * Accessor for status
     */
    public function getStatusAttribute()
    {
        return $this->ctr_status == '2' ? 'active' : 'inactive';
    }
}
