<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 *
 * @property int $id
 * @property int $client_id
 * @property int $gateway_id
 * @property int $product_id
 * @property string|null $external_id ID returned by the payment gateway
 * @property int $status Current state of the transaction
 * @property int $amount Total value in cents
 * @property int $quantity Number of items purchased
 * @property string $card_last_numbers Last 4 digits of the credit card
 * @property string $status_name Accessor for human-readable status
 */
class Transaction extends Model
{
    protected $appends = ['status_name'];
    
    /** @var int Initial state before gateway processing */
    const STATUS_PENDING  = 0;

    /** @var int Successfully processed by the gateway */
    const STATUS_PAID     = 1;

    /** @var int Rejected by the gateway or system error */
    const STATUS_FAILED   = 2;

    /** @var int Successfully refunded to the client */
    const STATUS_REFUNDED = 3;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id', 
        'gateway_id', 
        'product_id', 
        'external_id', 
        'status', 
        'amount', 
        'quantity', 
        'card_last_numbers'
    ];

    /**
     * Accessor to get the string representation of the status.
     * * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PAID     => 'PAID',
            self::STATUS_FAILED   => 'FAILED',
            self::STATUS_REFUNDED => 'REFUNDED',
            default               => 'PENDING',
        };
    }

    /**
     * @return BelongsTo<Product, Transaction>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Client, Transaction>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<Gateway, Transaction>
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}