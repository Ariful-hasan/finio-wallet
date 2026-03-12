<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Repositories;

use App\Domain\Wallet\Entities\Wallet;
use App\Domain\Wallet\Entities\Transaction;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Domain\Wallet\ValueObjects\OwnerId;
use App\Domain\Wallet\ValueObjects\TransactionId;
use App\Domain\Wallet\ValueObjects\Money;
use App\Domain\Wallet\ValueObjects\TransactionType;
use App\Infrastructure\Wallet\Models\WalletModel;
use App\Infrastructure\Wallet\Models\TransactionModel;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final class EloquentWalletRepository implements WalletRepositoryInterface
{
    public function findById(WalletId $id): ?Wallet
    {
        $model = WalletModel::find($id->value());

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByOwnerId(OwnerId $ownerId): ?Wallet
    {
        $model = WalletModel::where('owner_id', $ownerId->value())->first();

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findByOwnerIdAndCurrency(OwnerId $ownerId, string  $currency): ?Wallet
    {
        $model = WalletModel::where('owner_id', $ownerId->value())
            ->where('currency', strtoupper($currency))
            ->first();

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function save(Wallet $wallet): void
    {
        DB::transaction(function () use ($wallet) {
            // 1. persist wallet
            WalletModel::updateOrCreate(
                ['id' => $wallet->id()->value()],
                [
                    'owner_id' => $wallet->ownerId()->value(),
                    'balance'  => $wallet->balance()->amount(),
                    'currency' => $wallet->balance()->currency(),
                    'frozen'   => $wallet->isFrozen(),
                ]
            );

            // 2. persist new transactions
            foreach ($wallet->transactions() as $transaction) {
                TransactionModel::create([
                    'id'        => $transaction->id()->value(),
                    'wallet_id' => $transaction->walletId()->value(),
                    'amount'    => $transaction->amount()->amount(),
                    'currency'  => $transaction->amount()->currency(),
                    'type'      => $transaction->type()->value,
                    'note'      => $transaction->note(),
                ]);
            }

            // 3. dispatch domain events
            foreach ($wallet->pullEvents() as $event) {
                event($event);
            }
        });
    }

    // -------------------------------------------------------
    // Mapping — Eloquent model → Domain object
    // -------------------------------------------------------

    private function toDomain(WalletModel $model): Wallet
    {
        return Wallet::reconstitute(
            id:        WalletId::fromString($model->id),
            ownerId:   OwnerId::fromString($model->owner_id),
            balance:   new Money($model->balance, $model->currency),
            frozen:    $model->frozen,
            createdAt: new DateTimeImmutable($model->created_at),
        );
    }
}
