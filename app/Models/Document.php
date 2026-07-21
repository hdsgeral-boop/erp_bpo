<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\User;

class Document extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type_id',
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'size',
        'ocr_content',
        'status',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
