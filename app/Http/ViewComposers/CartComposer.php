<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use App\Models\ProductDetail;
use App\Models\Cart;

/* Đây là một lớp PHP tạo một trình tổng hợp giỏ hàng mới và liên kết dữ liệu với dạng xem. */
class CartComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    /*`protected ;` đang khai báo thuộc tính được bảo vệ có tên `` cho lớp `CartComposer`.
    Thuộc tính này được sử dụng để lưu trữ một thể hiện của lớp `Cart`, được tạo trong
    hàm tạo của lớp `CartComposer`. Phiên bản `Giỏ hàng` sau đó được sử dụng để cập nhật giỏ hàng
    data và liên kết nó với dạng xem trong phương thức `compose`. Từ khóa `được bảo vệ` chỉ ra rằng
    tài sản chỉ có thể được truy cập trong lớp và các lớp con của nó.*/
    protected $cart;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    /**
     * Chức năng này khởi tạo một đối tượng Giỏ hàng mới và cập nhật nó với bất kỳ dữ liệu hiện có nào từ
     * phiên họp.
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
        $cart = new Cart($oldCart);
        $cart->update();
        $this->cart = $cart;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
  /**
    * Đây là một chức năng PHP tạo một khung nhìn với một đối tượng giỏ hàng.
    *
    * @param View view Tham số là một thể hiện của lớp Illuminate\View\View, mà
    * đại diện cho chế độ xem được hiển thị. Nó được chuyển đến phương thức soạn thảo của một trình soạn thảo xem, mà
    * chịu trách nhiệm thêm dữ liệu vào chế độ xem trước khi hiển thị.
    */
    public function compose(View $view)
    {
        $view->with('cart', $this->cart);
    }
}
