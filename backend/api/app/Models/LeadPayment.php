<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LeadPayment extends Model
{
    use HasUuids;
    protected $table = 'lead_payments';

    protected $fillable = [
        'lead_id',
        'payment_type',
        'status',
        'sp3k_status',
        'sp3k_document',
        'sp3k_bank',
        'sp3k_code',
        'sp3k_date',
        'sp3k_number',
        'akad_kredit_status',
        'akad_kredit_penandatanganan_document',
        'notes',
        'proposed_name_1',
        'proposed_name_2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function selectedBanks()
    {
        return $this->hasMany(PaymentSelectedBank::class);
    }

    public function checklists()
    {
        return $this->hasMany(PaymentCheklist::class, 'payment_id');
    }
}
