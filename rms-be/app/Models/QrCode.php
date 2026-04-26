<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['table_id', 'venue_id', 'code_url', 'is_active', 'generated_at'])]
class QrCode extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'qr_id';

    protected function casts(): array { return ['is_active' => 'boolean', 'generated_at' => 'datetime']; }

    public function table(): BelongsTo { return $this->belongsTo(TableUnit::class, 'table_id'); }
    public function venue(): BelongsTo { return $this->belongsTo(Venue::class, 'venue_id'); }
}
