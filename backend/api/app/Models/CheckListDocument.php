<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CheckListDocument extends Model
{
    use HasUuids;
    protected $table = 'check_list_documents';

    protected $fillable = [
        'id',
        'lead_document_id',
        'name',
        'checked',
    ];

    public function leadDocument()
    {
        return $this->belongsTo(CollectionDocument::class, 'lead_document_id');
    }
}
