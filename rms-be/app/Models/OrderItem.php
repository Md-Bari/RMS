<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'item_id', 'quantity', 'unit_price', 'special_instruction'])]
class OrderItem extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'order_item_id';

    protected function casts(): array { return ['unit_price' => 'decimal:2']; }

    public function order(): BelongsTo { return $this->belongsTo(Order::class, 'order_id'); }
    public function menuItem(): BelongsTo { return $this->belongsTo(MenuItem::class, 'item_id'); }
}
