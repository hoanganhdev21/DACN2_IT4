<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Product;
use App\Models\Advertise;
use App\Models\Post;

/* Lớp SearchController xử lý các yêu cầu đến để tìm kiếm sản phẩm, bài đăng và
quảng cáo dựa trên khóa tìm kiếm.*/
class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Chức năng này tìm kiếm các sản phẩm, bài đăng và quảng cáo dựa trên một từ khóa tìm kiếm được cung cấp trong
     * yêu cầu và trả về kết quả trong một dạng xem.
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu, đại diện cho một HTTP
     * yêu cầu được thực hiện cho ứng dụng. Nó chứa thông tin về yêu cầu như HTTP
     *, URL, tiêu đề và bất kỳ dữ liệu nào được gửi trong phần thân yêu cầu.
     *
     * @return Nếu yêu cầu có một khóa tìm kiếm và nó không phải là null, hàm sẽ trả về một dạng xem được gọi là
     * 'pages.search' với một mảng dữ liệu chứa 5 đối tượng Quảng cáo mới nhất bắt đầu
     * ngày nhỏ hơn hoặc bằng ngày hiện tại, ngày kết thúc lớn hơn hoặc bằng ngày hiện tại
     * date và at_home_page được đặt thành false, 19 đối tượng Sản phẩm mới nhất có tên
     */
    public function __invoke(Request $request)
    {
        if($request->has('search_key') && $request->search_key != null) {
            $advertises = Advertise::where([
              ['start_date', '<=', date('Y-m-d')],
              ['end_date', '>=', date('Y-m-d')],
              ['at_home_page', '=', false]
            ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

            $products = Product::select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')
            ->where('name', 'LIKE', '%' . $request->search_key . '%')
            ->whereHas('product_detail', function (Builder $query) {
                $query->where('quantity', '>', 0);
            })
            ->with(['product_detail' => function($query) {
              $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
            }])->latest()->limit(19)->get();

            $posts = Post::select('id', 'title', 'image', 'created_at')
            ->where('title', 'LIKE', '%' . $request->search_key . '%')->get();

            return view('pages.search')->with(['data' => ['advertises' => $advertises, 'posts' => $posts, 'products' => $products]]);
        } else {
            return redirect()->route('home_page');
        }
    }
}
