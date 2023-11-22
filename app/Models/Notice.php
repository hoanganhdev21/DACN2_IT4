<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp Thông báo mở rộng lớp Mô hình và có một phương thức user() trả về một thuộc tính
mối quan hệ với mô hình Người dùng. */
class Notice extends Model
{
 /**
  * Hàm này xác định mối quan hệ trong đó mô hình thuộc về người dùng trong ứng dụng PHP.
  *
  * @return Mối quan hệ giữa mô hình hiện tại và mô hình "Người dùng" đang được trả về.
  * Cụ thể, mối quan hệ "thuộc về" đang được xác định, cho biết rằng mô hình hiện tại
  * thuộc về một phiên bản duy nhất của mô hình "Người dùng".
  */
  public function user() {
    return $this->belongsTo('App\User');
  }
}
