<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait UsesUuidPrimaryKey
{
    protected static function bootUsesUuidPrimaryKey(): void
    {
        static::creating(function ($model): void {
            $key = $model->getKeyName();

            if (! $model->{$key}) {
                $model->{$key} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
