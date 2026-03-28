<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubContractor extends Model
{
    use HasUuids;

    protected $table = 'sub_contractors';

    protected $fillable = [
        'name',
        'created_at',
    ];

    
}
