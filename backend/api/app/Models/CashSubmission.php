<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CashSubmission extends Model
{
    use HasUuids;

    protected $table = 'cash_flow_submissions';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'type',
        'description',
        'amount',
        'notes',
        'status',
        'feedback',
        'submitted_by',
        'file_proof',
        'approved_by',
        'approved_at',
    ];
}
