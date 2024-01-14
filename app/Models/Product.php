<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'brands_id', 'name', 'slug', 'sku', 'description', 'image', 'quantity','sizes_id','buying_price','selling_price', 'is_visible', 'is_featured',  'published_at'
    ];

    public function brands(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function units()
    {
        return $this->belongsTo(Unit::class);
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
    public function sizes(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
    public function unitConvertions(): BelongsToMany
    {
        return $this->belongsToMany(unitConvertion::class);
    }
}