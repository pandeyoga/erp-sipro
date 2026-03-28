<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    use HasUuids;

    
    protected $fillable = [
        'project_id',
        'name',
        'block_code',
        'notes'
    ];
}
