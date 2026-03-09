<?php

declare(strict_types=1);

namespace App\Application\Wallet\Handlers;

use App\Application\Wallet\Commands\OpenWalletCommand;
use App\Domain\Wallet\Entities\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Domain\Wallet\ValueObjects\OwnerId;

final class OpenWalletHandler
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    public function handle(OpenWalletCommand $command): void
    {
        $ownerId = OwnerId::fromString($command->ownerId);

        $existing = $this->walletRepository->findByOwnerIdAndCurrency(
            $ownerId,
            $command->currency
        );

        if ($existing !== null) {
            throw new \DomainException(
                "Owner already has a {$command->currency} wallet."
            );
        }

        $wallet = Wallet::open(
            ownerId:  $ownerId,
            currency: $command->currency,
        );

        $this->walletRepository->save($wallet);
    }
}
