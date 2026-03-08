<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Entities;

use App\Domain\Wallet\ValueObjects\TransactionId;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\Money;
use App\Domain\Wallet\ValueObjects\TransactionType;
use DateTimeImmutable;

final class Transaction
{
    private function __construct(
        private readonly TransactionId  $id,
        private readonly WalletId       $walletId,
        private readonly Money          $amount,
        private readonly TransactionType $type,
        private readonly DateTimeImmutable $createdAt,
        private readonly ?string        $note,
    ) {}

    public static function record(
        WalletId         $walletId,
        Money            $amount,
        TransactionType  $type,
        ?string          $note = null,
    ): self {
        return new self(
            id:        TransactionId::generate(),
            walletId:  $walletId,
            amount:    $amount,
            type:      $type,
            createdAt: new DateTimeImmutable(),
            note:      $note,
        );
    }

    public static function reconstitute(
        TransactionId    $id,
        WalletId         $walletId,
        Money            $amount,
        TransactionType  $type,
        DateTimeImmutable $createdAt,
        ?string          $note,
    ): self {
        return new self(
            id:        $id,
            walletId:  $walletId,
            amount:    $amount,
            type:      $type,
            createdAt: $createdAt,
            note:      $note,
        );
    }

    public function id(): TransactionId
    {
        return $this->id;
    }

    public function walletId(): WalletId
    {
        return $this->walletId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function type(): TransactionType
    {
        return $this->type;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function note(): ?string
    {
        return $this->note;
    }
}
