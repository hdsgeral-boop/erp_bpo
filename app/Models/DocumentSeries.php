<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSeries extends Model
{
    protected $fillable = [
        'document_type',
        'identifier',
        'description',
        'current_number',
        'is_active',
        'is_default',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    // Regra de negócio para obter o próximo número
    public function getNextNumber()
    {
        return $this->current_number + 1;
    }
    
    // Regra de negócio para consumir um número e formatar
    public function consumeNextNumber()
    {
        $this->current_number += 1;
        $this->save();
        
        // Formato: TIPO IDENTIFIER/NUMERO (Ex: FT 2024/1)
        return "{$this->document_type} {$this->identifier}/{$this->current_number}";
    }
}
