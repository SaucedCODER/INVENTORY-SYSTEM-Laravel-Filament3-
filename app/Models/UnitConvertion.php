<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitConvertion extends Model
{
    use HasFactory;

    protected $fillable = [
        'definition', 'base_unit','multiplier', 'toUnit'
    ];
    
    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
