<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as BaseActivity;

class Activity extends BaseActivity
{
    public function scopeForOrganization($query, $organization)
    {
        return $query->whereHas('causer', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        });
    }
}