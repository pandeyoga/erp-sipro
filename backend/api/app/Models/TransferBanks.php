<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransferBanks extends Model
{

    use HasUuids;

    protected $fillable = [
        'parent_cash_in_id',
        'parent_cash_out_id',
        'from_bank_account_id',
        'to_bank_account_id',
        'amount',
        'notes',
    ];
}
