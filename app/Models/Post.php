<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp "Post" mở rộng lớp "Model" và có một phương thức "người dùng" trả về một mối quan hệ với
lớp "Người dùng". */
class Post extends Model
{
  /**
   * Hàm này xác định mối quan hệ trong đó mô hình thuộc về người dùng trong ứng dụng PHP Laravel.
   *
   * @return Mã đang trả về mối quan hệ giữa mô hình hiện tại và mô hình "Người dùng".
   * Cụ thể, nó đang trả về mối quan hệ "thuộc về", cho biết rằng mô hình hiện tại
   * thuộc về một phiên bản duy nhất của mô hình "Người dùng".
   */
  public function user() {
    return $this->belongsTo('App\User');
  }
}
