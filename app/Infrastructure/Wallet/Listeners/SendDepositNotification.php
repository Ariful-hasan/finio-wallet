<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Listeners;

use App\Domain\Wallet\Events\MoneyDeposited;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendDepositNotification implements ShouldQueue
{
    public function handle(MoneyDeposited $event): void
    {
        // send push notification, email, etc.
        // $event->walletId, $event->amount available
    }
}
