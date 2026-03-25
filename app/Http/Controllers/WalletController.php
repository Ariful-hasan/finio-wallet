<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Wallet\Commands\OpenWalletCommand;
use App\Application\Wallet\Commands\DepositMoneyCommand;
use App\Application\Wallet\Commands\TransferMoneyCommand;
use App\Application\Wallet\Handlers\OpenWalletHandler;
use App\Application\Wallet\Handlers\DepositMoneyHandler;
use App\Application\Wallet\Handlers\TransferMoneyHandler;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Domain\Wallet\ValueObjects\WalletId;
use App\Http\Requests\OpenWalletRequest;
use App\Http\Requests\DepositMoneyRequest;
use App\Http\Requests\TransferMoneyRequest;
use Illuminate\Http\JsonResponse;

final class WalletController extends Controller
{
    public function __construct(
        private readonly OpenWalletHandler         $openWalletHandler,
        private readonly DepositMoneyHandler       $depositMoneyHandler,
        private readonly TransferMoneyHandler      $transferMoneyHandler,
        private readonly WalletRepositoryInterface $walletRepository,
    ) {}

    public function open(OpenWalletRequest $request): JsonResponse
    {
        $this->openWalletHandler->handle(
            new OpenWalletCommand(
                ownerId:  $request->user()->id,
                currency: strtoupper($request->currency),
            )
        );

        return response()->json([
            'message' => 'Wallet opened successfully.',
        ], 201);
    }

    public function deposit(
        DepositMoneyRequest $request,
        string              $walletId,
    ): JsonResponse {
        $this->depositMoneyHandler->handle(
            new DepositMoneyCommand(
                walletId: $walletId,
                amount:   $request->amount,
                currency: strtoupper($request->currency),
            )
        );

        return response()->json([
            'message' => 'Deposit successful.',
        ]);
    }

    public function transfer(
        TransferMoneyRequest $request,
        string               $walletId,
    ): JsonResponse {
        $this->transferMoneyHandler->handle(
            new TransferMoneyCommand(
                fromWalletId: $walletId,
                toWalletId:   $request->to_wallet_id,
                amount:       $request->amount,
                currency:     strtoupper($request->currency),
            )
        );

        return response()->json([
            'message' => 'Transfer successful.',
        ]);
    }

    public function show(string $walletId): JsonResponse
    {
        $wallet = $this->walletRepository->findById(
            WalletId::fromString($walletId)
        );

        if ($wallet === null) {
            return response()->json([
                'message' => 'Wallet not found.',
            ], 404);
        }

        return response()->json([
            'id'       => $wallet->id()->value(),
            'owner_id' => $wallet->ownerId()->value(),
            'balance'  => [
                'amount'   => $wallet->balance()->amount(),
                'currency' => $wallet->balance()->currency(),
            ],
            'frozen'     => $wallet->isFrozen(),
            'created_at' => $wallet->createdAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
