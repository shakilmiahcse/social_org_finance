<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignAdjustment extends Model
{
    protected $fillable = [
        'campaign_fund_id', 'main_fund_id', 'amount', 'type', 'note', 'created_by', 'updated_by'
    ];

    public function campaignFund()
    {
        return $this->belongsTo(Fund::class, 'campaign_fund_id');
    }

    public function mainFund()
    {
        return $this->belongsTo(Fund::class, 'main_fund_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
