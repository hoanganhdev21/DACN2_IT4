<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp ProductImage thuộc về mô hình ProductDetail trong ứng dụng PHP. */
class ProductImage extends Model
{
  public function product_detail() {
    return $this->belongsTo('App\Models\ProductDetail');
  }
}
