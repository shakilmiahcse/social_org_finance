<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'organization_id', 'guard_name'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
