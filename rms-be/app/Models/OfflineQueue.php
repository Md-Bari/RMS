<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['venue_id', 'order_id', 'payload', 'status', 'created_at', 'processed_at'])]
class OfflineQueue extends Model
{
    use UsesUuidPrimaryKey;

    public $timestamps = false;

    protected $table = 'offline_queue';
    protected $primaryKey = 'queue_id';

    protected function casts(): array { return ['payload' => 'array', 'created_at' => 'datetime', 'processed_at' => 'datetime']; }
}
