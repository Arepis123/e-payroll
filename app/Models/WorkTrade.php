<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WorkTrade Model
 *
 * READ-ONLY MODEL
 * This table is from the second database (worker_db) managed by another system.
 * Contains master data for worker positions/trades.
 */
class WorkTrade extends Model
{
    /**
     * The connection name for the model.
     * This points to the second database (worker_db)
     */
    protected $connection = 'worker_db';

    /**
     * The table associated with the model.
     */
    protected $table = 'mst_worktrade';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'trade_id';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'trade_desc',
        'wtr_status',
    ];

    /**
     * Get workers with this trade/position
     */
    public function workers()
    {
        return $this->hasMany(Worker::class, 'wkr_wtrade', 'trade_id');
    }

    /**
     * Accessor for trade description
     */
    public function getDescriptionAttribute()
    {
        return $this->trade_desc;
    }
}
