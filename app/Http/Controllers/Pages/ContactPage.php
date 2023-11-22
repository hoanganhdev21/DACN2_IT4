<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advertise;

class ContactPage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   /**
     * Hàm PHP này truy xuất tối đa 5 quảng cáo hiện đang hoạt động và không được đặt thành
     * xuất hiện trên trang chủ và chuyển chúng đến dạng xem cho trang liên hệ.
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
     * yêu cầu được thực hiện cho ứng dụng. Nó chứa thông tin về yêu cầu như HTTP
     * phương thức, tiêu đề và tham số. Trong trường hợp này, nó không được sử dụng trong chức năng và chỉ
     * được bao gồm dưới dạng tham số vì nó được yêu cầu bởi __
     *
     * @return Chế độ xem có tên "pages.contact" đang được trả về cùng với một mảng đối tượng Quảng cáo có
     * các thuộc tính "product_id", "title" và "image" đáp ứng các điều kiện đã chỉ định. mảng
     * được chuyển đến chế độ xem dưới dạng một biến có tên là "quảng cáo".
     */
    public function __invoke(Request $request)
    {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        return view('pages.contact')->with(['advertises' => $advertises]);
    }
}
