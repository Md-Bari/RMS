<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['venue_id', 'name', 'sort_order', 'is_active'])]
class MenuCategory extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'category_id';

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function venue(): BelongsTo { return $this->belongsTo(Venue::class, 'venue_id'); }
    public function items(): HasMany { return $this->hasMany(MenuItem::class, 'category_id'); }
}
