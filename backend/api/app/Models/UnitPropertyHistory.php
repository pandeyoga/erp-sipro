<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UnitPropertyHistory extends Model
{
    use HasUuids;

    protected $table = 'unit_property_histories';

    protected $fillable = [
        'unit_property_id',
        'action_by',
        'old_status',
        'new_status',
        'changed_at',
        'notes',
    ];

}
