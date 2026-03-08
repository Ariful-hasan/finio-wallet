<?php

declare(strict_types=1);

namespace App\Domain\Wallet\Repositories;

use App\Domain\Wallet\Entities\Wallet;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\OwnerId;

interface WalletRepositoryInterface
{
    public function findById(WalletId $id): ?Wallet;

    public function findByOwnerId(OwnerId $ownerId): ?Wallet;

    public function findByOwnerIdAndCurrency(OwnerId $ownerId, string  $currency): ?Wallet;

    public function save(Wallet $wallet): void;
}
