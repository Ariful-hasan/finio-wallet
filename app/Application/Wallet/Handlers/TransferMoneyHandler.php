<?php

declare(strict_types=1);

namespace App\Application\Wallet\Handlers;

use App\Application\Wallet\Commands\TransferMoneyCommand;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Domain\Wallet\Services\TransferService;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\Money;

final class TransferMoneyHandler
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly TransferService           $transferService,
    ) {}

    public function handle(TransferMoneyCommand $command): void
    {
        $fromWalletId = WalletId::fromString($command->fromWalletId);
        $toWalletId   = WalletId::fromString($command->toWalletId);

        $from = $this->walletRepository->findById($fromWalletId);
        $to   = $this->walletRepository->findById($toWalletId);

        if ($from === null) {
            throw new \DomainException(
                "Source wallet {$command->fromWalletId} not found."
            );
        }

        if ($to === null) {
            throw new \DomainException(
                "Destination wallet {$command->toWalletId} not found."
            );
        }

        $money = new Money($command->amount, $command->currency);

        $this->transferService->transfer($from, $to, $money);

        $this->walletRepository->save($from);
        $this->walletRepository->save($to);
    }
}
