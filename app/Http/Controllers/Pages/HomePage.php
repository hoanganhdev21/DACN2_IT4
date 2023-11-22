<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Product;
use App\Models\Producer;
use App\Models\ProductDetail;
use App\Models\Advertise;
use App\Models\Post;

/* Lớp HomePage là bộ điều khiển xử lý các yêu cầu đến và trả về dữ liệu cho trang chủ
của một trang web, bao gồm các sản phẩm, sản phẩm yêu thích, nhà sản xuất, quảng cáo và bài đăng. */
class HomePage extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  /**
   * Hàm này lấy và trả về dữ liệu cho trang chủ của một website bao gồm sản phẩm,
   * sản phẩm, nhà sản xuất, quảng cáo và bài đăng yêu thích.
   *
   * @return chế độ xem có tên 'pages.home' với một mảng dữ liệu bao gồm sản phẩm, sản phẩm yêu thích,
   * bài đăng, quảng cáo và nhà sản xuất.
   */
  public function __invoke()
  {

    $products = Product::select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')
    ->whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->limit(9)->get();

    $favorite_products = Product::select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')
    ->whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->orderBy('rate', 'DESC')->limit(10)->get();

    $producers = Producer::select('id', 'name')->get();

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', true]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $posts = Post::select('id', 'title', 'image', 'created_at')->latest()->limit(4)->get();

    return view('pages.home')->with('data',['products' => $products, 'favorite_products'=>$favorite_products, 'posts'=> $posts, 'advertises' => $advertises, 'producers' => $producers]);
  }
}
