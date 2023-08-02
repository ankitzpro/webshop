<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProducts extends Model
{
    use HasFactory;

    public function orders()
    {
        return $this->hasMany('App\Models\Orders', 'id', 'order_id');
	
    }
    
    public function products()
    {
        return $this->hasMany('App\Models\Products', 'id', 'product_id')->select('id', 'product_name', 'price');
    }
}
