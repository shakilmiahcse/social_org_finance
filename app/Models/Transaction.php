<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'txn_id', 'donor_id', 'fund_id', 'amount', 'type', 'purpose',
        'payment_method', 'reference', 'note', 'status',
        'created_by', 'updated_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments()
    {
        return $this->hasMany(TransactionAttachment::class);
    }

}
