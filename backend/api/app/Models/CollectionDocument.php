<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CollectionDocument extends Model
{
    use HasUuids;
    protected $fillable = [
        'id',   
        'lead_id',
        'phone_partner',
        'emergency_contact_name',
        'emergency_contact_phone',
        'supervisor_name',
        'supervisor_phone',
        'location_share',
        'status',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'collection_document_id', 'id');
    }

    public function checkList()
    {
        return $this->hasMany(CheckListDocument::class, 'lead_document_id', 'id');
    }
}
