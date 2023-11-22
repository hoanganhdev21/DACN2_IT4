<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp Comment mở rộng lớp Model và định nghĩa hai phương thức để thiết lập mối quan hệ với
các mô hình Người dùng và Sản phẩm trong một ứng dụng PHP. */
class Comment extends Model
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
  /**
   * Hàm này xác định mối quan hệ trong đó mô hình thuộc về mô hình sản phẩm trong PHP Laravel
   * ứng dụng.
   *
   * @return Mối quan hệ `belongsTo` của mô hình hiện tại với mô hình `Product` đang được
   * trả lại. Điều này có nghĩa là mô hình hiện tại thuộc về một phiên bản mô hình `Sản phẩm` duy nhất.
   */
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }
}
