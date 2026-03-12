<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WalletModel extends Model
{
    protected $table = 'wallets';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'owner_id',
        'balance',
        'currency',
        'frozen',
    ];

    protected $casts = [
        'frozen'  => 'boolean',
        'balance' => 'integer',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(TransactionModel::class, 'wallet_id');
    }
}
