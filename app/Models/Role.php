<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'organization_id', 'guard_name'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    protected static function booted()
    {
        $organization_id = request()->session()->get("organization_id");

        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check() && $organization_id) {
                $builder->where('organization_id', $organization_id);
            }
        });
    }
}
