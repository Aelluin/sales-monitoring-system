<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // If your database table has a different name, you can specify it here
    protected $table = 'products'; // Make sure this matches your database table name

    // Specify which attributes are mass assignable
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
    ];
}
