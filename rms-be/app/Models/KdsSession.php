<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['venue_id', 'user_id', 'device_fingerprint', 'is_online', 'last_ping_at'])]
class KdsSession extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'session_id';

    protected function casts(): array { return ['is_online' => 'boolean', 'last_ping_at' => 'datetime']; }
}
