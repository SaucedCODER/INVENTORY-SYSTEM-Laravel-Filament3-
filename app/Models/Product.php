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
        'brand_id', 'name', 'slug', 'sku', 'description', 'image', 'quantity','unit_id','size_id','buying_price','selling_price', 'is_visible', 'is_featured',  'published_at'
    ];

    public function brands(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class)->withTimestamps();
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }
    public function sizes()
    {
        return $this->belongsTo(Size::class);
    }
}