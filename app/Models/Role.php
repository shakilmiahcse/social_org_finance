<?php

namespace App\Models;

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
        static::addGlobalScope('organization', function (Builder $builder) {
            // শুধুমাত্র অথেন্টিকেটেড ইউজারদের জন্য এবং যখন organization_id থাকে
            if (auth()->check() && auth()->user()->organization_id) {
                $builder->where('organization_id', auth()->user()->organization_id);
            }
        });

        // নতুন রোল ক্রিয়েশনের সময় ভ্যালিডেশন
        static::creating(function ($role) {
            if (Role::withoutGlobalScope('organization')
                ->where('name', $role->name)
                ->where('organization_id', $role->organization_id)
                ->where('guard_name', $role->guard_name)
                ->exists()
            ) {
                throw new \Exception("A role '{$role->name}' already exists for this organization and guard.");
            }
        });
    }
}
