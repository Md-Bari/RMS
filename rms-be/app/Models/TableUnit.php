<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['venue_id', 'label', 'section', 'is_active'])]
class TableUnit extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'table_id';

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function venue(): BelongsTo { return $this->belongsTo(Venue::class, 'venue_id'); }
    public function qrCode(): HasOne { return $this->hasOne(QrCode::class, 'table_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'table_id'); }
}
