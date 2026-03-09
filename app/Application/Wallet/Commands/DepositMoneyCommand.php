<?php

declare(strict_types=1);

namespace App\Application\Wallet\Commands;

final class DepositMoneyCommand
{
    public function __construct(
        public readonly string $walletId,
        public readonly int    $amount,
        public readonly string $currency,
    ) {}
}
