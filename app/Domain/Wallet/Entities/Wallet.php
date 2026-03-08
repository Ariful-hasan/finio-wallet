<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Entities;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Wallet\Events\MoneyDeposited;
use App\Domain\Wallet\Events\WalletFrozen;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\OwnerId;
use App\Domain\Wallet\ValueObjects\Money;
use App\Domain\Wallet\ValueObjects\TransactionType;
use App\Domain\Wallet\Exceptions\InsufficientFundsException;
use App\Domain\Wallet\Exceptions\WalletFrozenException;
use DateTimeImmutable;

final class Wallet
{
    private array $transactions = [];
    private array $domainEvents = [];

    private function __construct(
        private readonly WalletId        $id,
        private readonly OwnerId         $ownerId,
        private Money                    $balance,
        private bool                     $frozen,
        private readonly DateTimeImmutable $createdAt,
    ) {}

    public static function open(OwnerId $ownerId, string $currency): self
    {
        return new self(
            id:        WalletId::generate(),
            ownerId:   $ownerId,
            balance:   new Money(0, $currency),
            frozen:    false,
            createdAt: new DateTimeImmutable(),
        );
    }

    public static function reconstitute(
        WalletId          $id,
        OwnerId           $ownerId,
        Money             $balance,
        bool              $frozen,
        DateTimeImmutable $createdAt,
    ): self {
        return new self(
            id:        $id,
            ownerId:   $ownerId,
            balance:   $balance,
            frozen:    $frozen,
            createdAt: $createdAt,
        );
    }

    // -------------------------------------------------------
    // Business Behaviours
    // -------------------------------------------------------

    public function deposit(Money $amount): void
    {
        $this->guardNotFrozen();

        $this->balance = $this->balance->add($amount);

        $this->transactions[] = Transaction::record(
            walletId: $this->id,
            amount:   $amount,
            type:     TransactionType::DEPOSIT,
        );

        $this->recordEvent(new MoneyDeposited($this->id, $amount));
    }

    public function debit(Money $amount): void
    {
        $this->guardNotFrozen();
        $this->guardSufficientFunds($amount);

        $this->balance = $this->balance->subtract($amount);

        $this->transactions[] = Transaction::record(
            walletId: $this->id,
            amount:   $amount,
            type:     TransactionType::TRANSFER_OUT,
        );
    }

    public function credit(Money $amount): void
    {
        $this->guardNotFrozen();

        $this->balance = $this->balance->add($amount);

        $this->transactions[] = Transaction::record(
            walletId: $this->id,
            amount:   $amount,
            type:     TransactionType::TRANSFER_IN,
        );
    }

    public function freeze(): void
    {
        $this->frozen = true;

        $this->recordEvent(new WalletFrozen($this->id));
    }

    public function unfreeze(): void
    {
        $this->frozen = false;
    }

    // -------------------------------------------------------
    // Guards — private business rule enforcers
    // -------------------------------------------------------

    private function guardNotFrozen(): void
    {
        if ($this->frozen) {
            throw new WalletFrozenException(
                "Wallet {$this->id} is frozen and cannot perform transactions."
            );
        }
    }

    private function guardSufficientFunds(Money $amount): void
    {
        if (!$this->balance->isGreaterThanOrEqual($amount)) {
            throw new InsufficientFundsException(
                "Insufficient funds in wallet {$this->id}."
            );
        }
    }

    //-----------------------------------------
    // Domain Events
    //-----------------------------------------
    public function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    public function id(): WalletId
    {
        return $this->id;
    }

    public function ownerId(): OwnerId
    {
        return $this->ownerId;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function transactions(): array
    {
        return $this->transactions;
    }
}
