<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['venue_id', 'actor_user_id', 'action_type', 'payload', 'ip_address', 'performed_at'])]
class AuditLog extends Model
{
    use UsesUuidPrimaryKey;

    public $timestamps = false;

    protected $primaryKey = 'log_id';

    protected function casts(): array { return ['payload' => 'array', 'performed_at' => 'datetime']; }
}
