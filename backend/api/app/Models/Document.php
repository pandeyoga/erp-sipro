<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasUuids;

    protected $table = 'documents';

    protected $fillable = [
        'lead_id',
        'collection_document_id',
        'type',
        'file_url',
        'status',
        'uploaded_at',
        'verified_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }
}
