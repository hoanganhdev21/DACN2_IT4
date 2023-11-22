<?php

namespace App\Models;

use App\Models\ProductDetail;
use Illuminate\Support\Arr;

/* Lớp Cart quản lý các mặt hàng, số lượng và giá của một giỏ hàng, cho phép thêm,
cập nhật và xóa các mục. */
class Cart
{
/* Đây là các thuộc tính của lớp `Cart` lưu trữ thông tin về các mặt hàng trong giỏ hàng. */  
  public $items = NULL;
  public $totalQty = 0;
  public $totalPrice = 0;

  /**
   * Đây là hàm khởi tạo khởi tạo các thuộc tính của đối tượng giỏ hàng với
   * giá trị từ một đối tượng giỏ hàng cũ nếu nó tồn tại.
   *
   * @param oldCart là một đối tượng đại diện cho trạng thái trước đó của giỏ hàng. Nó
   * chứa thông tin về các mặt hàng trong giỏ hàng, tổng số lượng mặt hàng và tổng giá
   * của các mặt hàng. Hàm tạo được sử dụng để khởi tạo một thể hiện mới của giỏ hàng
   * đối tượng với các giá trị từ
   */
  public function __construct($oldCart)
  {
    if($oldCart) {
      $this->items = $oldCart->items;
      $this->totalQty = $oldCart->totalQty;
      $this->totalPrice = $oldCart->totalPrice;
    }
  }

  /**
   * Chức năng thêm một mặt hàng vào giỏ hàng với số lượng, giá và ID của nó.
   *
   * @param item Mặt hàng đang được thêm vào giỏ hàng.
   * @param id Tham số id là mã định danh duy nhất của mặt hàng được thêm vào giỏ hàng.
   * @param qty Số lượng mặt hàng được thêm vào giỏ hàng.
   *
   * @return một giá trị boolean (đúng hoặc sai) tùy thuộc vào việc mục đã được thêm thành công vào
   * giỏ hàng hay không.
   */
  public function add($item, $id, $qty) {

    $this->update();

    if(($item->promotion_price > 0) && ($item->promotion_start_date <= date('Y-m-d')) && ($item->promotion_end_date >= date('Y-m-d')))
      $storedItem = ['qty' => 0, 'price' => $item->promotion_price, 'item' => $item];
    else
      $storedItem = ['qty' => 0, 'price' => $item->sale_price, 'item' => $item];

    if($this->items && array_key_exists($id, $this->items)) {
      if(($this->items[$id]['qty'] + $qty) > $this->items[$id]['item']->quantity)
        return false;
      else
        $storedItem = $this->items[$id];
    }

    $storedItem['qty'] += $qty;

    $this->items[$id] = $storedItem;
    $this->totalQty += $qty;
    $this->totalPrice += $qty * $this->items[$id]['price'];
    return true;
  }

  /**
   * Hàm cập nhật số lượng mặt hàng trong giỏ hàng và trả về giá trị true nếu cập nhật là
   * thành công, sai khác.
   *
   * @param id Tham số id là mã định danh duy nhất của mục cần được cập nhật trong
   * xe đẩy.
   * @param qty qty là số lượng, là số lượng mặt hàng mà người dùng muốn cập nhật cho
   * một mặt hàng cụ thể trong giỏ hàng.
   *
   * @return một giá trị boolean. Nó trả về true nếu số lượng của mặt hàng với id đã cho là
   * đã cập nhật thành công và sai nếu số lượng mới lớn hơn số lượng hiện có của
   * mục.
   */
  public function updateItem($id, $qty) {
    $this->update();
    if($qty > $this->items[$id]['item']->quantity)
      return false;
    else {
      $increase = $qty - $this->items[$id]['qty'];
      $this->items[$id]['qty'] = $qty;
      $this->totalPrice = $this->totalPrice + $increase * $this->items[$id]['price'];
      $this->totalQty = $this->totalQty + $increase;
      return true;
    }
  }

  /**
   * Chức năng này cập nhật tổng giá và chi tiết mặt hàng của một giỏ hàng.
   *
   * @return một giá trị boolean. Nó trả về true nếu tổng số lượng mặt hàng khác 0 và các mặt hàng
   * đã được cập nhật thành công và sai nếu tổng số lượng bằng không.
   */
  public function update() {
    if($this->totalQty == 0) {
      return false;
    } else {
      $this->totalPrice = 0;
      foreach($this->items as $key => $item) {
        $product = ProductDetail::where('id',$key)->with(['product' => function($query) {
          $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
        }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();
        $this->items[$key]['item'] = $product;
        if(($product->promotion_price > 0) && ($product->promotion_start_date <= date('Y-m-d')) && ($product->promotion_end_date >= date('Y-m-d')))
          $this->items[$key]['price'] = $product->promotion_price;
        else
          $this->items[$key]['price'] = $product->sale_price;
        $this->totalPrice = $this->totalPrice + $this->items[$key]['price'] * $this->items[$key]['qty'];
      }
      return true;
    }
  }

  /**
   * Hàm này xóa một mặt hàng khỏi mảng và cập nhật tổng số lượng và giá.
   *
   * @param id Tham số `` là mã định danh duy nhất của mục cần xóa khỏi
   * giỏ hàng. Nó được sử dụng để định vị mục trong mảng `` và xóa mục đó.
   *
   * @return một giá trị boolean. Nó trả về true nếu mục có id đã cho được xóa thành công
   * từ giỏ hàng và sai nếu không tìm thấy mặt hàng trong giỏ hàng.
   */
  public function remove($id) {
    if($this->items && array_key_exists($id, $this->items)) {
      $qty = $this->items[$id]['qty'];
      $price = $this->items[$id]['price'];
      $this->items = Arr::except($this->items, $id);
      $this->totalQty = $this->totalQty - $qty;
      $this->totalPrice = $this->totalPrice - $qty * $price;
      $this->update();
      return true;
    } else {
      return false;
    }
  }
}
