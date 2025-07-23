<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class CampaignAdjustment extends Model
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
        'campaign_fund_id', 'main_fund_id', 'amount', 'type', 'note', 'created_by', 'updated_by', 'organization_id'
    ];

    public function fund()
    {
        return $this->belongsTo(Fund::class, 'campaign_fund_id');
    }

    public function mainFund()
    {
        return $this->belongsTo(Fund::class, 'main_fund_id');
    }

    public function campaignFund()
    {
        return $this->belongsTo(Fund::class, 'campaign_fund_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
