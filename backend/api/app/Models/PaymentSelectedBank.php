<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentSelectedBank extends Model
{

    use HasUuids;
    
    protected $table = 'payment_selected_banks';
    
    protected $fillable = [
        'payment_id',
        'bank_code',
    ];
}
