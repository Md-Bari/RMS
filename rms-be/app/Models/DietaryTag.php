<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'color_code'])]
class DietaryTag extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'tag_id';

    public function menuItems(): BelongsToMany { return $this->belongsToMany(MenuItem::class, 'menu_item_tags', 'tag_id', 'item_id'); }
}
