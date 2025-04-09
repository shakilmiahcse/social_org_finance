<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'created_by', 'updated_by'
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
        return self::pluck('name', 'id')->map(function ($name, $id) {
            return ['id' => $id, 'name' => $name];
        });
    }
}
