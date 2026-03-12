<?php

declare(strict_types=1);

namespace App\Infrastructure\Wallet\Models;

use Illuminate\Database\Eloquent\Model;

final class TransactionModel extends Model
{
    protected $table = 'transactions';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'wallet_id',
        'amount',
        'currency',
        'type',
        'note',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];
}
