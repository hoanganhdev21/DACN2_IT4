<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * param vote rate
 * return string html start vote
 */
// Helpers class chứa các phương thức tĩnh để tạo các phần tử HTML và kiểm tra các tuyến đang hoạt động trong một ứng dụng PHP.
class Helpers
{
  /**
   * Hàm trả về một chuỗi mã HTML biểu thị xếp hạng sao dựa trên xếp hạng số đã cho.
   * 
   * @param rate Tham số "tỷ lệ" là một giá trị số biểu thị xếp hạng từ 0 đến 5.
   * 
   * @return một chuỗi mã HTML hiển thị xếp hạng sao dựa trên tham số đầu vào ``. Mã HTML bao gồm các biểu tượng phông chữ tuyệt vời đại diện cho sao và nửa sao.
   */
  // Phương thức đánh giá sao.
  public static function get_start_vote($rate) {
    $output = '';
    if($rate == 0){
      $output = '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 0 && $rate < 1) {
      $output = '<i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 1) {
      $output = '<i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 1 && $rate < 2) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 2) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 2 && $rate < 3) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 3) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 3 && $rate < 4) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i>';
    } elseif ($rate == 4) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 4 && $rate < 5) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>';
    } elseif ($rate == 5) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
    }
    return $output;
  }
  /**
   * The function calculates the percentage of promotion discount based on the original price and
   * promotion price, and returns it as a string in a specific format if the promotion is currently
   * active.
   * 
   * @param price The original price of the product.
   * @param promotion_price The discounted price of the product during a promotion.
   * @param promotion_start_date The start date of the promotion, in the format 'Y-m-d'.
   * @param promotion_end_date The parameter "promotion_end_date" is a date value that represents the
   * end date of a promotion. It is used in the function to check if the current date is within the
   * promotion period, and if so, calculate the percentage discount for the product.
   * 
   * @return a string containing the promotion percentage if the promotion is currently active (based
   * on the promotion start and end dates) and the promotion price is not null. If the promotion is not
   * active or the promotion price is null, an empty string is returned.
   */

  //  Phương thức nhận phần trăm khuyến mãi.
  public static function get_promotion_percent($price, $promotion_price, $promotion_start_date, $promotion_end_date) {
    $output = '';
    if($promotion_price != null && $promotion_start_date <= date('Y-m-d') && $promotion_end_date >= date('Y-m-d')) {
      $output = '<div class="promotion-percent">Giảm ' . round(100 * ($price - $promotion_price) / $price) . '%</div>';
    }
    return $output;
  }
  // Phương thức lấy giá thật.
  public static function get_real_price($price, $promotion_price, $promotion_start_date, $promotion_end_date) {
    $output = '';
    if($promotion_price != null && $promotion_start_date <= date('Y-m-d') && $promotion_end_date >= date('Y-m-d'))
      $output = '<strong>'.number_format($promotion_price,0,',','.').'₫</strong><del>'.number_format($price,0,',','.').'₫</del>';
    else
      $output = '<strong>'.number_format($price,0,',','.').'₫</strong>';
    return $output;
  }
  // Phương thức lấy url ảnh đại diện.
  public static function get_image_avatar_url($image = null) {
    if($image != null)
      return asset('storage/images/avatars/'.$image);
    else
      return asset('images/no_avatar.jpg');
  }
  // Phương thức lấy url ảnh sản phẩm
  public static function get_image_product_url($image = null) {
    if($image != null)
      return asset('storage/images/products/'.$image);
    else
      return asset('images/no_image.png');
  }
  // Phương thức lấy url quảng cáo hình ảnh.
  public static function get_image_advertise_url($image = null) {
    if($image != null)
      return asset('storage/images/advertises/'.$image);
    else
      return asset('images/no_image.png');
  }
  // Phương thức lấy url ảnh bài viết.
  public static function get_image_post_url($image = null) {
    if($image != null)
      return asset('storage/images/posts/'.$image);
    else
      return asset('images/no_image.png');
  }
// Phương thức kiểm tra hoạt động.
  public static function check_active($array_route) {
    $name_route = request()->route()->getName();
    return in_array($name_route, $array_route) ? 'active' : null;
  }
  // Phương thức kiểm tra thông số hoạt động.
  public static function check_param_active($route, $id) {
    $name_route = request()->route()->getName();
    $param = request()->route()->parameter('id');
    return ($name_route == $route) && ($param == $id) ? 'active' : null;
  }
}
