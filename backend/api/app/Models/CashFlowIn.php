<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CashFlowIn extends Model
{
    use HasUuids;
    protected $fillable = [
        'property_id',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'description',
        'parent_id',
        'total_amount',
        'paid_amount',
        'bank_account_id',
        'notes',
    ];

    // cast uuids
    protected $casts = [
        'id' => 'string',
        'property_id' => 'string',
        'category_id' => 'string',
        'sub_category_id' => 'string',
        'sub_sub_category_id' => 'string',
        'parent_id' => 'string',
    ];
}