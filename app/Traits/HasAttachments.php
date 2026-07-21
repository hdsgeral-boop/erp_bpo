<?php

namespace App\Traits;

use App\Models\Attachment;

trait HasAttachments
{
    /**
     * Get all of the model's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
