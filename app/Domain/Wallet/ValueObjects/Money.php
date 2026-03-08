<?php

declare(strict_types=1);

namespace App\Domain\Wallet\ValueObjects;

use InvalidArgumentException;

final class Money
{
    public function __construct(
        private readonly int $amount,   // stored in cents, never floats
        private readonly string $currency
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException(
                "Money amount cannot be negative."
            );
        }

        if (empty(trim($this->currency))) {
            throw new InvalidArgumentException(
                "Currency cannot be empty."
            );
        }
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return strtoupper($this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function add(Money $other): self
    {
        $this->guardSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->guardSameCurrency($other);

        if ($other->amount > $this->amount) {
            throw new InvalidArgumentException(
                "Cannot subtract more than available amount."
            );
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function isGreaterThanOrEqual(Money $other): bool
    {
        $this->guardSameCurrency($other);

        return $this->amount >= $other->amount;
    }

    private function guardSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Currency mismatch: {$this->currency} vs {$other->currency}."
            );
        }
    }
}
