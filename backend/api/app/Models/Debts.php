<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Debts extends Model
{
    use HasUuids;
    
    protected $table = 'debts';

    protected $fillable = [
        'name',
        'description',
        'cash_in_sub_sub_category_id',
        'bank_account_id',
        'payment_bank_account_id',
        'total_amount',
        'paid_amount',
        'cash_in_id',
        'cash_out_id',
        'created_by',
    ];
}
