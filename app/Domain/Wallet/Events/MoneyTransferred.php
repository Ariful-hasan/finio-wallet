<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\Money;

final class MoneyTransferred extends DomainEvent
{
    public function __construct(
        public readonly WalletId $fromWalletId,
        public readonly WalletId $toWalletId,
        public readonly Money    $amount,
    ) {
        parent::__construct();
    }
}
