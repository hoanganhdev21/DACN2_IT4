<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp ProductDetail là mô hình đại diện cho các chi tiết của một sản phẩm, bao gồm cả hình ảnh của nó
và chi tiết đặt hàng. */
class ProductDetail extends Model
{
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }
  public function product_images() {
    return $this->hasMany('App\Models\ProductImage');
  }
  public function order_details() {
    return $this->hasMany('App\Models\OrderDetail');
  }
}
