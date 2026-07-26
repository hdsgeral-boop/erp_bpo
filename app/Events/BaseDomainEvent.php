<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaseDomainEvent
{
    use Dispatchable, SerializesModels;

    public int $companyId;
    public string $eventType;
    public string $title;
    public string $message;
    public array $options;
    public $recipients;

    public function __construct(
        int $companyId,
        string $eventType,
        string $title,
        string $message,
        array $options = [],
        $recipients = null
    ) {
        $this->companyId = $companyId;
        $this->eventType = $eventType;
        $this->title = $title;
        $this->message = $message;
        $this->options = $options;
        $this->recipients = $recipients;
    }
}
