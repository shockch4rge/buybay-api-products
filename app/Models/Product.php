<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasUuids;

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }
}
