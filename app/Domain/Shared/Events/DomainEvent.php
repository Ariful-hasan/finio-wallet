<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

abstract class DomainEvent
{
    public readonly string $eventId;
    public readonly DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->eventId    = Uuid::uuid4()->toString();
        $this->occurredAt = new DateTimeImmutable();
    }
}
