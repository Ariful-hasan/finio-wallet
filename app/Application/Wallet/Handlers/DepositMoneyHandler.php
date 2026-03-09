<?php

declare(strict_types=1);

namespace App\Application\Wallet\Handlers;

use App\Application\Wallet\Commands\DepositMoneyCommand;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\Money;

final class DepositMoneyHandler
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    public function handle(DepositMoneyCommand $command): void
    {
        $walletId = WalletId::fromString($command->walletId);

        $wallet = $this->walletRepository->findById($walletId);

        if ($wallet === null) {
            throw new \DomainException(
                "Wallet {$command->walletId} not found."
            );
        }

        $money = new Money($command->amount, $command->currency);

        $wallet->deposit($money);

        $this->walletRepository->save($wallet);
    }
}
