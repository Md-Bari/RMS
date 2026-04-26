<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['venue_id', 'order_id', 'recipient_type', 'delivery_method', 'content_snapshot', 'sent_at', 're_alert_at', 'acknowledged_at'])]
class Notification extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'notif_id';

    protected function casts(): array { return ['sent_at' => 'datetime', 're_alert_at' => 'datetime', 'acknowledged_at' => 'datetime']; }

    public function venue(): BelongsTo { return $this->belongsTo(Venue::class, 'venue_id'); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class, 'order_id'); }
}
