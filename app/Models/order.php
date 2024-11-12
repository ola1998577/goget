<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['pivot'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, 'order_products')
    //                 ->withPivot('quantity', 'price')
    //                 ->withTimestamps();
    // }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
                    ->withTimestamps(); // فقط جلب العلاقات بين Order و Product بدون pivot
    }

    public function color(){
        return $this->belongsTo(product_color::class);
    }

    public function size(){
        return $this->belongsTo(product_size::class);
    }

    public function promo_code(){
        return $this->belongsTo(promo_code::class);
    }

    public function location(){
        return $this->belongsTo(location_user::class);
    }

    public function delivery_company(){
        return $this->belongsTo(delivery_company::class);
    }

    public function driver(){
        return $this->belongsTo(driver::class);
    }
}
