<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use HasFactory;

    use SoftDeletes;
    public function products()
    {
        return $this->belongsToMany('App\Models\Products', 'order_products', 'order_id', 'product_id')->select( 'product_name', 'price');
	
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customers','customer_id')->select('id','full_name','email','job_title');
	
    }
}
