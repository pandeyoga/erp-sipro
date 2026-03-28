<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CashFlowOut extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'description',
        'total_amount',
        'paid_amount',
        'bank_account_id',
        'notes',
    ];

    // cast uuids
    protected $casts = [
        'id' => 'string',
        'category_id' => 'string',
        'sub_category_id' => 'string',
    ];
}