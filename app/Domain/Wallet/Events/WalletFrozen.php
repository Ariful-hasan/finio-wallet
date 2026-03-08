<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Wallet\ValueObjects\WalletId;

final class WalletFrozen extends DomainEvent
{
    public function __construct(
        public readonly WalletId $walletId,
    ) {
        parent::__construct();
    }
}
