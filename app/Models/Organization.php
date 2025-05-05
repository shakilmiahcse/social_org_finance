<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'logo_path', 'website', 'timezone',
        'currency', 'is_active', 'trial_ends_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
