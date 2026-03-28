<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentCheklist extends Model
{
    use HasUuids;
    protected $table = 'payment_cheklists';

    protected $fillable = [
        'payment_id',
        'code',
        'checked',
    ];

    public function LeadPayment()
    {
        return $this->belongsTo(LeadPayment::class);
    }
}
