<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Specify which attributes are mass assignable
    protected $fillable = [
        'product_id', 'quantity', 'total_price', 'payment_method',
        'customer_name', 'customer_email', 'customer_address', 'user_id',
    ];

    // Define the relationship between Sale and Product
    public function product()
    {
        return $this->belongsTo(Product::class); // Sale belongs to a Product
    }

    public function user() {
        return $this->belongsTo(User::class); // Assuming the Sale model belongs to the User model
    }

}

