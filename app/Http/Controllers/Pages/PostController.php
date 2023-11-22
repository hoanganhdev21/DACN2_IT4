<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Advertise;
use App\Models\Post;
use App\Models\Product;

/* Lớp PostController truy xuất và hiển thị dữ liệu cho một trang web bao gồm quảng cáo,
các sản phẩm và bài đăng được đề xuất, cũng như thông tin cho một bài đăng. */
class PostController extends Controller
{
  /**
   * Hàm này lấy và trả về dữ liệu cho một trang web có hiển thị quảng cáo, gợi ý
   * sản phẩm, và bài viết.
   *
   * @return chế độ xem có tên 'pages.posts' với một mảng dữ liệu bao gồm 'quảng cáo', 'bài đăng' và
   * 'đề_xuất_sản_phẩm'.
   */
  public function index()
  {
    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $suggest_products = Product::select('id','name', 'image', 'rate')
    ->whereHas('product_details', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->limit(4)->get();

    $posts = Post::select('id', 'title', 'image', 'created_at')->latest()->paginate(11);

    return view('pages.posts')->with(['data' => ['advertises' => $advertises, 'posts' => $posts, 'suggest_products' => $suggest_products]]);
  }

  /**
   * Chức năng này truy xuất và hiển thị thông tin về một bài đăng, bao gồm các quảng cáo liên quan,
   * sản phẩm được đề xuất và bài viết được đề xuất.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa HTTP
   * thông tin yêu cầu như phương thức yêu cầu, tiêu đề và dữ liệu đầu vào. Nó được sử dụng để lấy
   * giá trị của tham số 'id' từ URL.
   *
   * @return chế độ xem có tên 'pages.post' với một mảng dữ liệu bao gồm 'quảng cáo', 'bài đăng',
   * 'suggest_products' và 'suggest_posts'.
   */
  public function show(Request $request)
  {
    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $suggest_products = Product::select('id','name', 'image', 'rate')
    ->whereHas('product_details', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->limit(4)->get();

    $post = Post::select('id', 'title', 'image', 'content', 'created_at')
      ->where('id', $request->id)->first();

    if(!$post) abort(404);

    $suggest_posts = Post::select('id', 'title', 'image', 'created_at')
      ->where('id', '<>', $post->id)->latest()->limit(5)->get();

    return view('pages.post')->with(['data' => ['advertises' => $advertises, 'post' => $post, 'suggest_products' => $suggest_products, 'suggest_posts' => $suggest_posts]]);
  }
}
