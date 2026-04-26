<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'subscription_tier', 'currency', 'timezone', 'is_active', 'brand_logo_url', 'welcome_banner', 'service_charge_pct'])]
class Venue extends Model
{
    use UsesUuidPrimaryKey;

    protected $primaryKey = 'venue_id';

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'service_charge_pct' => 'decimal:2'];
    }

    public function staffUsers(): HasMany { return $this->hasMany(StaffUser::class, 'venue_id'); }
    public function tables(): HasMany { return $this->hasMany(TableUnit::class, 'venue_id'); }
    public function menuCategories(): HasMany { return $this->hasMany(MenuCategory::class, 'venue_id'); }
    public function menuItems(): HasMany { return $this->hasMany(MenuItem::class, 'venue_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class, 'venue_id'); }
}
