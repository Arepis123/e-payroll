<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'contractor_clab_no',
        'user_name',
        'user_email',
        'module',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'properties',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject (polymorphic relation)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get changes in a human-readable format
     */
    public function getChangesAttribute(): array
    {
        if (empty($this->old_values) && empty($this->new_values)) {
            return [];
        }

        $changes = [];

        // Get all changed fields
        $allKeys = array_unique(array_merge(
            array_keys($this->old_values ?? []),
            array_keys($this->new_values ?? [])
        ));

        foreach ($allKeys as $key) {
            $old = $this->old_values[$key] ?? null;
            $new = $this->new_values[$key] ?? null;

            if ($old !== $new) {
                $changes[$key] = [
                    'old' => $old,
                    'new' => $new,
                ];
            }
        }

        return $changes;
    }

    /**
     * Scope to filter by module
     */
    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by contractor
     */
    public function scopeContractor($query, string $clabNo)
    {
        return $query->where('contractor_clab_no', $clabNo);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
