<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['venue_id', 'phone_number', 'created_at', 'anonymized_at'])]
class Customer extends Model
{
    use UsesUuidPrimaryKey;

    public $timestamps = false;

    protected $primaryKey = 'customer_id';

    protected function casts(): array { return ['created_at' => 'datetime', 'anonymized_at' => 'datetime']; }

    public function venue(): BelongsTo { return $this->belongsTo(Venue::class, 'venue_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'customer_id'); }
}
