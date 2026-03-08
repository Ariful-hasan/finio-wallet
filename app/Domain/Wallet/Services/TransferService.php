<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Services;

use App\Domain\Wallet\Entities\Wallet;
use App\Domain\Wallet\ValueObjects\Money;
use App\Domain\Wallet\Events\MoneyTransferred;
use App\Domain\Wallet\Exceptions\CurrencyMismatchException;

final class TransferService
{
    public function transfer(
        Wallet $from,
        Wallet $to,
        Money  $amount,
    ): void {
        $this->guardSameCurrency($from, $to, $amount);

        $from->debit($amount);
        $to->credit($amount);

        $from->recordEvent(
            new MoneyTransferred(
                fromWalletId: $from->id(),
                toWalletId:   $to->id(),
                amount:       $amount,
            )
        );
    }

    private function guardSameCurrency(
        Wallet $from,
        Wallet $to,
        Money  $amount,
    ): void {
        if (
            $from->balance()->currency() !== $amount->currency() ||
            $to->balance()->currency() !== $amount->currency()
        ) {
            throw new CurrencyMismatchException(
                "Currency mismatch between wallets or transfer amount."
            );
        }
    }
}
