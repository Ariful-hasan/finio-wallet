<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Listeners;

use App\Domain\Wallet\Events\MoneyTransferred;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendTransferNotification implements ShouldQueue
{
    public function handle(MoneyTransferred $event): void
    {
        // notify both sender and receiver
        // $event->fromWalletId, $event->toWalletId, $event->amount
    }
}
