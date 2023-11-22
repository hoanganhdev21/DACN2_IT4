<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/* Lớp ProductVote là một mô hình trong PHP đại diện cho phiếu bầu của người dùng đối với một sản phẩm, có thể điền
các thuộc tính cho nội dung, tỷ lệ, ID người dùng và ID sản phẩm và thuộc về sản phẩm và người dùng. */
class ProductVote extends Model
{
 /* `protected ` là một mảng chỉ định thuộc tính nào của mô hình `ProductVote` có thể
  được phân công hàng loạt. Nói cách khác, nó cho phép các thuộc tính này được đặt hàng loạt bằng cách sử dụng `create`
  hoặc phương thức `cập nhật`. Trong trường hợp này, các thuộc tính `content`, `rate`, `user_id` và `product_id`
  có thể được gán hàng loạt. */
  protected $fillable = [
      'content', 'rate', 'user_id', 'product_id'
  ];
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
  /**
   * Hàm này xác định mối quan hệ trong đó mô hình thuộc về người dùng trong ứng dụng PHP.
   *
   * @return Mối quan hệ `belongsTo` giữa mô hình hiện tại và mô hình `User` đang được
   * trả lại. Điều này có nghĩa là mô hình hiện tại thuộc về một người dùng và mối quan hệ có thể được
   * được sử dụng để truy xuất mô hình người dùng được liên kết.
   */
  public function user() {
    return $this->belongsTo('App\Models\User');
  }
}
