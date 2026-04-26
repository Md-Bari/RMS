<?php

namespace App\Models;

use App\Models\Concerns\UsesUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['venue_id', 'generated_by', 'period_type', 'date_from', 'date_to', 'format', 'file_url', 'created_at'])]
class SalesReport extends Model
{
    use UsesUuidPrimaryKey;

    public $timestamps = false;

    protected $primaryKey = 'report_id';

    protected function casts(): array { return ['date_from' => 'date', 'date_to' => 'date', 'created_at' => 'datetime']; }
}
