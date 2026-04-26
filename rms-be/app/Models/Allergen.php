<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'icon_code'])]
class Allergen extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'allergen_id';

    public function menuItems(): BelongsToMany { return $this->belongsToMany(MenuItem::class, 'menu_item_allergens', 'allergen_id', 'item_id'); }
}
