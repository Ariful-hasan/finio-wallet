<?php

declare(strict_types=1);

namespace App\Application\Wallet\Commands;

final class TransferMoneyCommand
{
    public function __construct(
        public readonly string $fromWalletId,
        public readonly string $toWalletId,
        public readonly int    $amount,
        public readonly string $currency,
    ) {}
}
