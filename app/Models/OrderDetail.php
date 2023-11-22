<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp OrderDetail là một mô hình thuộc về Đơn hàng và Chi tiết sản phẩm. */
class OrderDetail extends Model
{
  /**
   * Hàm này xác định mối quan hệ trong đó một mô hình thuộc về một đơn đặt hàng trong ứng dụng Laravel.
   *
   * @return Hàm `order()` đang trả về mối quan hệ `belongsTo` giữa mô hình hiện tại
   * và mô hình `Đặt hàng` trong không gian tên `App\Models`. Mối quan hệ này chỉ ra rằng hiện tại
   * mô hình thuộc về một phiên bản của mô hình `Đặt hàng`.
   */
  public function order() {
    return $this->belongsTo('App\Models\Order');
  }
  /**
   * Hàm này xác định mối quan hệ trong đó một chi tiết sản phẩm thuộc về một sản phẩm.
   *
   * @return Mối quan hệ `belongsTo` của mô hình `ProductDetail` đang được trả về.
   */
  public function product_detail() {
    return $this->belongsTo('App\Models\ProductDetail');
  }
}
