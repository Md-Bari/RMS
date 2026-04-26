<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['order_id', 'refund_initiated_by', 'stripe_payment_intent_id', 'amount', 'currency', 'status', 'method', 'paid_at', 'refunded_at'])]
class Payment extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'payment_id';

    protected function casts(): array { return ['amount' => 'decimal:2', 'paid_at' => 'datetime', 'refunded_at' => 'datetime']; }

    public function order(): BelongsTo { return $this->belongsTo(Order::class, 'order_id'); }
    public function receipt(): HasOne { return $this->hasOne(Receipt::class, 'payment_id'); }
}
