<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp Producer mở rộng lớp Model và có phương thức truy xuất nhiều sản phẩm được liên kết
với nó. */
class Producer extends Model
{
   /**
    * Chức năng này xác định mối quan hệ một-nhiều giữa kiểu máy hiện tại và "Sản phẩm"
    * trong một ứng dụng PHP.
    *
    * @return Mối quan hệ giữa mô hình hiện tại và mô hình "Sản phẩm" đang được trả về.
    * Cụ thể, mối quan hệ "hasMany" đang được thiết lập, cho biết rằng mô hình hiện tại
    * có thể có nhiều phiên bản của mô hình "Sản phẩm" được liên kết với nó.
    */
    public function products() {
      return $this->hasMany('App\Models\Product');
    }
}
