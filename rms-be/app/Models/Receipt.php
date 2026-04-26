<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['payment_id', 'delivery_channel', 'delivered_at'])]
class Receipt extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'receipt_id';

    protected function casts(): array { return ['delivered_at' => 'datetime']; }

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class, 'payment_id'); }
}
