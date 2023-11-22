<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp PaymentMethod có mối quan hệ với mô hình Đơn hàng nơi nó có nhiều đơn hàng. */class PaymentMethod extends Model
{
  /**
   * Chức năng này xác định mối quan hệ một-nhiều giữa mô hình hiện tại và mô hình "Đặt hàng"
   * trong một ứng dụng PHP.
   *
   * @return Hàm `orders()` đang trả về mối quan hệ `hasMany` giữa mô hình hiện tại
   * và mô hình `Đặt hàng`. Điều này có nghĩa là mô hình hiện tại có nhiều đơn đặt hàng được liên kết với nó.
   */
  public function orders() {
    return $this->hasMany('App\Models\Order');
  }
}
