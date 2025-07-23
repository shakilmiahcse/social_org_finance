<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Donor extends Model
{
    use LogsActivity;

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'content'])
            ->logOnlyDirty();
    }
    protected $fillable = [
        'name', 'email', 'phone', 'blood_group', 'address', 'created_by', 'updated_by', 'organization_id'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getDropdown()
    {
        $organization_id = request()->session()->get("organization_id");

        return self::where('organization_id', $organization_id)
        ->pluck('name', 'id')
        ->map(function ($name, $id) {
            return ['id' => $id, 'name' => $name];
        })
        ->values();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
