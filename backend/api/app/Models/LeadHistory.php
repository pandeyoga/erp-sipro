<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LeadHistory extends Model
{
    use HasUuids;

    protected $table = 'leads_histories';
    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'action_by',
        'old_status',
        'new_status',
        'changed_at',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}
