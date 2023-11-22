<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp Khuyến mãi mở rộng lớp Mô hình và có một phương thức để truy xuất Sản phẩm được liên kết
người mẫu. */
class Promotion extends Model
{
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }
}
