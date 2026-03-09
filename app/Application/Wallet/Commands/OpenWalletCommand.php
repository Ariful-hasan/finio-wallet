<?php

declare(strict_types=1);

namespace App\Application\Wallet\Commands;

final class OpenWalletCommand
{
    public function __construct(
        public readonly string $ownerId,
        public readonly string $currency,
    ) {}
}
