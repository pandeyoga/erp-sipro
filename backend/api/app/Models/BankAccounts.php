<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    use HasUuids;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'code',
        'name',
        'account_number',
        'opening_balance',
    ];
}
