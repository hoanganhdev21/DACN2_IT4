<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Producer;

/* Đây là một lớp PHP soạn tiêu đề của một dạng xem bằng cách truy xuất danh sách các nhà sản xuất từ ​​một
cơ sở dữ liệu và chuyển nó đến dạng xem. */
class HeaderComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    /* `protected ;` đang khai báo thuộc tính được bảo vệ có tên `` trong
    Lớp `HeaderComposer`. Thuộc tính này được sử dụng để lưu trữ danh sách các nhà sản xuất được lấy từ
    cơ sở dữ liệu. Nó có thể được truy cập trong lớp và các lớp con của nó, nhưng không thể truy cập bên ngoài chúng.*/
    protected $producers;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    /**
     * Hàm PHP này khởi tạo biến "nhà sản xuất" bằng cách chọn cột "id" và "name"
     * từ bảng "Nhà sản xuất".
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        $this->producers = Producer::select('id', 'name')->get();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    
/**
     * Hàm PHP này chuyển một biến gọi là "nhà sản xuất" tới một dạng xem.
     *
     * @param View view Tham số là một thể hiện của lớp Illuminate\View\View, mà
     * đại diện cho chế độ xem được hiển thị. Nó được chuyển đến phương thức soạn thảo của một trình soạn thảo xem, mà
     * chịu trách nhiệm thêm dữ liệu vào chế độ xem trước khi hiển thị.
     */
    public function compose(View $view)
    {
        $view->with('producers', $this->producers);
    }
}
