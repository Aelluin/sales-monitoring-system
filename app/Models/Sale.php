<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Specify which attributes are mass assignable
    protected $fillable = [
        'product_id',
        'quantity',
        'payment_method',
        'total_price', // If you are saving the total price
    ];

    // Define the relationship between Sale and Product
    public function product()
    {
        return $this->belongsTo(Product::class); // Sale belongs to a Product
    }
}

