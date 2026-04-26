<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['item_id', 's3_url', 'sort_order', 'uploaded_at'])]
class MenuItemPhoto extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'photo_id';

    protected function casts(): array { return ['uploaded_at' => 'datetime']; }

    public function item(): BelongsTo { return $this->belongsTo(MenuItem::class, 'item_id'); }
}
