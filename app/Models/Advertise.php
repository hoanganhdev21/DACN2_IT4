<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp Advertise là một mô hình thuộc về mô hình Sản phẩm trong ứng dụng PHP. */
class Advertise extends Model
{
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
