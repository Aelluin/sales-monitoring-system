<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['product_id', 'quantity', 'total_price'];

    // Define the relationship between a sale and a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
