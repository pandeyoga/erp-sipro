<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MarketingTask extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id',
        'lead_id',
        'task',
        'description',
        'is_ontime',
        'due_date',
        'completed_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
