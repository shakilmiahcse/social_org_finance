<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = [
        'name', 'description', 'type', 'total_amount', 'created_by', 'updated_by', 'organization_id'
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

        return self::whereNull('closed_at')->where('organization_id', $organization_id)->pluck('name', 'id')->map(function ($name, $id) {
            return ['id' => $id, 'name' => $name];
        });
    }

    public static function getCampaignDropdown()
    {
        $organization_id = request()->session()->get("organization_id");

        return self::whereNull('closed_at')->where('type', 'campaign')->where('organization_id', $organization_id)->pluck('name', 'id')->map(function ($name, $id) {
            return ['id' => $id, 'name' => $name];
        });
    }

    public static function getMainDropdown()
    {
        $organization_id = request()->session()->get("organization_id");

        return self::where('organization_id', $organization_id)
        ->pluck('name', 'id')
        ->map(function ($name, $id) {
            return ['id' => $id, 'name' => $name];
        })
        ->values();
    }
}
