<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/* Lớp Order mở rộng lớp Model và định nghĩa các mối quan hệ với User, PaymentMethod và
Các mô hình OrderDetail, cũng như phương thức getStatus trả về trạng thái của đơn hàng dựa trên
mảng được xác định trước. */
/* Lớp Order mở rộng lớp Model và định nghĩa các mối quan hệ với User, PaymentMethod và
Các mô hình OrderDetail, cũng như phương thức getStatus trả về trạng thái của đơn hàng dựa trên
mảng được xác định trước. */
class Order extends Model
{
  /**
   * Hàm này xác định mối quan hệ trong đó mô hình thuộc về người dùng trong ứng dụng PHP Laravel.
   *
   * @return Mối quan hệ `belongsTo` giữa mô hình hiện tại và mô hình `User` đang được
   * trả lại. Điều này có nghĩa là mô hình hiện tại thuộc về một người dùng và mối quan hệ có thể được
   * được sử dụng để truy xuất mô hình người dùng được liên kết.
   */
  public function user() {
    return $this->belongsTo('App\Models\User');
  }
  /**
   * Hàm này xác định mối quan hệ giữa mô hình hiện tại và mô hình Phương thức thanh toán trong một
   * Ứng dụng PHP.
   *
   * @return Mối quan hệ `belongsTo` của mô hình `PaymentMethod` đang được trả về.
   */
  public function payment_method() {
    return $this->belongsTo('App\Models\PaymentMethod');
  }
  /**
   * Chức năng này xác định mối quan hệ một-nhiều giữa mô hình hiện tại và "OrderDetail"
   * trong một ứng dụng PHP.
   *
   * @return Hàm `order_details()` đang trả về mối quan hệ `hasMany` giữa hiện tại
   * và mô hình `OrderDetail`. Điều này có nghĩa là mô hình hiện tại có nhiều trường hợp của
   * Mô hình `OrderDetail` được liên kết với nó.
   */
  public function order_details() {
    return $this->hasMany('App\Models\OrderDetail');
  }
  /* `protected = [''];` đang xác định một mảng các thuộc tính không thể gán hàng loạt. Cái này
  có nghĩa là các thuộc tính này không thể được đặt hàng loạt bằng cách sử dụng các phương thức `create()` hoặc `update()`. TRONG
  trường hợp này, chuỗi rỗng `''` chỉ ra rằng không có thuộc tính nào được bảo vệ, nghĩa là tất cả
  các thuộc tính có thể được gán hàng loạt. */
  protected $guarded = [''];

  /* `protected ` là một mảng xác định các giá trị trạng thái có thể có cho một đơn đặt hàng và giá trị của chúng
  các lớp CSS và tên hiển thị tương ứng. Các khóa của mảng là các giá trị trạng thái (1, 2, 3,
  -1) và các giá trị là các mảng chứa lớp CSS và tên hiển thị cho từng giá trị trạng thái.
  Mảng này được sử dụng trong phương thức `getStatus()` để trả về lớp CSS và tên hiển thị cho
  giá trị trạng thái hiện tại của một đơn đặt hàng. */
  protected $status = [
    '1' => [
        'class' => 'default',
        'name'  => 'Đang Xử Lý'
    ],

    '2' => [
      'class' => 'info',
      'name'  => 'Đang Vận Chuyển'
    ],

    '3' => [
      'class' => 'success',
      'name'  => 'Đã Giao Hàng'
    ],

    '-1' => [
      'class' => 'danger',
      'name'  => 'Hủy'
    ],

  ];

 /**
   * Hàm này trả về giá trị của mảng "trạng thái" tại khóa "[N\A]".
   *
   * @return giá trị của mảng "trạng thái" tại khóa "[N\A]". Nếu không có khóa như vậy trong mảng,
   * nó sẽ trả về chuỗi "[N\A]".
   */
  public function getStatus(){
    return Arr::get($this->status,"[N\A]");
  }
  
}
