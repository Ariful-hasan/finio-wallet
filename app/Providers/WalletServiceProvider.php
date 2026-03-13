<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Infrastructure\Wallet\Repositories\EloquentWalletRepository;
use App\Domain\Wallet\Services\TransferService;
use App\Application\Wallet\Handlers\OpenWalletHandler;
use App\Application\Wallet\Handlers\DepositMoneyHandler;
use App\Application\Wallet\Handlers\TransferMoneyHandler;
use App\Domain\Wallet\Events\MoneyDeposited;
use App\Domain\Wallet\Events\MoneyTransferred;
use App\Domain\Wallet\Events\WalletFrozen;
use App\Infrastructure\Wallet\Listeners\LogWalletFrozen;
use App\Infrastructure\Wallet\Listeners\SendDepositNotification;
use App\Infrastructure\Wallet\Listeners\SendTransferNotification;
use Closure;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // bind interface → implementation
        $this->app->bind(
            WalletRepositoryInterface::class,
            EloquentWalletRepository::class,
        );
    }

   public function boot(): void
    {
        Event::listen(MoneyDeposited::class,   SendDepositNotification::class);
        Event::listen(MoneyTransferred::class, SendTransferNotification::class);
        Event::listen(WalletFrozen::class,     LogWalletFrozen::class);
    }
}
