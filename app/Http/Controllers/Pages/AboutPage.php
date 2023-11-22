<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Advertise;

class AboutPage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Hàm PHP này truy xuất tối đa 5 quảng cáo hiện đang hoạt động và không được đặt thành
     * xuất hiện trên trang chủ và chuyển chúng đến chế độ xem "giới thiệu".
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
     * yêu cầu được thực hiện cho ứng dụng. Nó chứa thông tin về yêu cầu như HTTP
     * phương thức, tiêu đề và tham số. Trong trường hợp này, nó không được sử dụng trong chức năng và chỉ
     * được bao gồm dưới dạng tham số vì nó được yêu cầu bởi __
     *
     * @return chế độ xem có tên 'pages.about' với một mảng đối tượng Quảng cáo có tên 'quảng cáo'. Các
     * Các đối tượng quảng cáo được lọc theo các thuộc tính start_date, end_date và at_home_page và bị giới hạn
     * đến 5. Chế độ xem có thể được sử dụng để hiển thị thông tin về trang web hoặc công ty.
     */
    public function __invoke(Request $request)
    {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        return view('pages.about')->with(['advertises' => $advertises]);
    }
}
