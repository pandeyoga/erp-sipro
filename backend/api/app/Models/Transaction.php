<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    
    protected $fillable = [
        'property_id',
        'reference_id',
        'type',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'amount',
        'notes',
        'bank_account_id',
    ];
}
