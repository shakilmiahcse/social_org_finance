<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Fund extends Model
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Calculate the current balance of the fund
     */
    public function getBalance(): float
    {
        return $this->transactions()
            ->where('status', 'completed')
            ->get()
            ->reduce(function ($balance, $transaction) {
                return $balance + ($transaction->type === 'credit' ? $transaction->amount : -$transaction->amount);
            }, 0);
    }

    /**
     * Get transactions with running balance (more efficient implementation)
     */
    public function getTransactionsWithRunningBalance()
    {
        $balance = 0;
        return $this->transactions()
            ->orderBy('created_at')
            ->with(['donor', 'createdBy'])
            ->get()
            ->map(function ($transaction) use (&$balance) {
                $balance += ($transaction->type === 'credit' ? $transaction->amount : -$transaction->amount);
                $transaction->running_balance = $balance;
                return $transaction;
            });
    }
}
