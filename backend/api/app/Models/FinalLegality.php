<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FinalLegality extends Model
{
    use HasUuids;

    protected $fillable = [
        'lead_id',
        'status',
        'bast_document',
        'bast_hanover_photo',
        'bast_date',
        'retention_document',
        'retention_hanover_photo',
        'retention_start_date',
        'retention_end_date',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
