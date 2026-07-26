<?php

namespace App\Listeners;

use App\Events\BaseDomainEvent;
use App\Services\Notifications\NotificationEngine;

class NotificationEventListener
{
    protected NotificationEngine $engine;

    public function __construct(NotificationEngine $engine)
    {
        $this->engine = $engine;
    }

    public function handle(BaseDomainEvent $event): void
    {
        $this->engine->dispatch(
            $event->companyId,
            $event->eventType,
            $event->title,
            $event->message,
            $event->options,
            $event->recipients
        );
    }
}
