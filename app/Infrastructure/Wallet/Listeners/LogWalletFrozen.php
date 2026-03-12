<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Listeners;

use App\Domain\Wallet\Events\WalletFrozen;
use Illuminate\Contracts\Queue\ShouldQueue;

final class LogWalletFrozen implements ShouldQueue
{
    public function handle(WalletFrozen $event): void
    {
        // write audit log
        // $event->walletId
    }
}
