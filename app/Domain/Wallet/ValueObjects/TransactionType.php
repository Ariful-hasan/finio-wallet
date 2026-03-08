<?php

declare(strict_types=1);

namespace App\Domain\Wallet\ValueObjects;

enum TransactionType: string
{
    case DEPOSIT      = 'deposit';
    case TRANSFER_IN  = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';

    public function label(): string
    {
        return match($this) {
            self::DEPOSIT      => 'Deposit',
            self::TRANSFER_IN  => 'Transfer In',
            self::TRANSFER_OUT => 'Transfer Out',
        };
    }

    public function isCredit(): bool
    {
        return match($this) {
            self::DEPOSIT,
            self::TRANSFER_IN  => true,
            self::TRANSFER_OUT => false,
        };
    }

    public function isDebit(): bool
    {
        return !$this->isCredit();
    }
}
