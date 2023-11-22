<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductDetail;
use App\Models\Producer;
use App\Models\Product;
use App\Models\Advertise;
use App\Models\ProductVote;

/* Lớp ProductsController xử lý các yêu cầu liên quan đến sản phẩm, bao gồm hiển thị sản phẩm,
lọc sản phẩm và thêm phiếu bầu sản phẩm. */
class ProductsController extends Controller
{
  /**
   * Chức năng này truy xuất và hiển thị danh sách các sản phẩm dựa trên các tiêu chí tìm kiếm khác nhau,
   * bao gồm tên, hệ điều hành và giá cả, đồng thời bao gồm cả quảng cáo và nhà sản xuất
   * thông tin.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa HTTP
   * thông tin yêu cầu như phương thức yêu cầu, tiêu đề và dữ liệu đầu vào. Nó được sử dụng để lấy
   * nhập dữ liệu từ người dùng và để kiểm tra xem các tham số nhất định có trong yêu cầu hay không.
   *
   * @return chế độ xem có tên 'pages.products' với một mảng dữ liệu bao gồm 'quảng cáo',
   * 'nhà sản xuất' và 'sản phẩm'.
   */
  public function index(Request $request) {

    if($request->has('type') && $request->input('type') == 'promotion') {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where([
          ['quantity', '>', 0],
          ['promotion_price', '>', 0],
          ['promotion_start_date', '<=', date('Y-m-d')],
          ['promotion_end_date', '>=', date('Y-m-d')]
        ]);
      });
    } else {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
      });
    }

    $query_products->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }]);

    if($request->has('name') && $request->input('name') != null)
      $query_products->where('name', 'LIKE', '%' . $request->input('name') . '%');

    if($request->has('os') && $request->input('os') != null)
      $query_products->where('OS', 'LIKE', '%' . $request->input('os') . '%');

    if($request->has('price') && $request->input('price') != null) {
      $min_price_query = ProductDetail::select('product_id', DB::raw('min(sale_price) as min_sale_price'))->where('quantity', '>', 0)->groupBy('product_id');

      $query_products->joinSub($min_price_query, 'min_price_query', function ($join) {
        $join->on('products.id', '=', 'min_price_query.product_id');
      })->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->orderBy('min_sale_price', $request->input('price'));
    } else {
      $query_products->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->latest();
    }

    if($request->has('type') && $request->input('type') == 'vote')
      $query_products->orderBy('rate', 'desc');

    $products = $query_products->paginate(15);

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $producers = Producer::select('id', 'name')->get();

    return view('pages.products')->with(['data' => ['advertises' => $advertises, 'producers' => $producers, 'products' => $products]]);
  }

  /**
   * Chức năng này truy xuất các sản phẩm từ cơ sở dữ liệu dựa trên các bộ lọc khác nhau và hiển thị chúng trên một
   * cùng với quảng cáo và thông tin về nhà sản xuất.
   *
   * @param Yêu cầu yêu cầu Tham số là một thể hiện của lớp Yêu cầu, chứa
   * thông tin về yêu cầu HTTP hiện tại, chẳng hạn như phương thức yêu cầu, tiêu đề và dữ liệu đầu vào.
   * @param id ID của nhà sản xuất có sản phẩm đang được truy xuất.
   *
   * @return chế độ xem có tên 'pages.producer' với một mảng dữ liệu và đối tượng nhà sản xuất.
   */
  public function getProducer(Request $request, $id) {

    if($request->has('type') && $request->input('type') == 'promotion') {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where([
          ['quantity', '>', 0],
          ['promotion_price', '>', 0],
          ['promotion_start_date', '<=', date('Y-m-d')],
          ['promotion_end_date', '>=', date('Y-m-d')]
        ]);
      });
    } else {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
      });
    }

    $query_products->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }]);

    if($request->has('name') && $request->input('name') != null)
      $query_products->where('name', 'LIKE', '%' . $request->input('name') . '%');

    if($request->has('os') && $request->input('os') != null)
      $query_products->where('OS', 'LIKE', '%' . $request->input('os') . '%');

    if($request->has('price') && $request->input('price') != null) {
      $min_price_query = ProductDetail::select('product_id', DB::raw('min(sale_price) as min_sale_price'))->where('quantity', '>', 0)->groupBy('product_id');

      $query_products->joinSub($min_price_query, 'min_price_query', function ($join) {
        $join->on('products.id', '=', 'min_price_query.product_id');
      })->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->orderBy('min_sale_price', $request->input('price'));
    } else {
      $query_products->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->latest();
    }

    if($request->has('type') && $request->input('type') == 'vote')
      $query_products->orderBy('rate', 'desc');

    $products = $query_products->where('producer_id', $id)->paginate(15);

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $producers = Producer::where('id', '<>', $id)->select('id', 'name')->get();
    $producer = Producer::select('id', 'name')->find($id);

    if(!$producer) abort(404);

    return view('pages.producer')->with(['data' => ['advertises' => $advertises, 'producers' => $producers, 'products' => $products], 'producer' => $producer]);
  }

  /**
   * Chức năng này lấy thông tin về một sản phẩm cụ thể, bao gồm thông tin chi tiết, hình ảnh,
   * khuyến mãi và các sản phẩm liên quan, cũng như quảng cáo và phiếu bầu của người dùng.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
   * tiêu đề và dữ liệu đầu vào. Nó được sử dụng để truy xuất dữ liệu từ yêu cầu, chẳng hạn như tham số truy vấn hoặc
   * dữ liệu biểu mẫu.
   * @param id Tham số `` là ID của sản phẩm đang được yêu cầu.
   *
   * @return chế độ xem có tên 'pages.product' với một mảng dữ liệu bao gồm 'quảng cáo', 'sản phẩm',
   * 'product_details', 'suggest_products' và 'product_votes'.
   */
  public function getProduct(Request $request, $id) {

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $product = Product::select('id', 'producer_id', 'name', 'sku_code', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate', 'information_details', 'product_introduction')
      ->whereHas('product_details', function (Builder $query) {
        $query->where('import_quantity', '>', 0);
      })
      ->where('id', $id)->with(['promotions' => function ($query) {
        $query->select('id', 'product_id', 'content')
              ->where([['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]])
              ->latest();
        }])->with(['producer' => function ($query) {
          $query->select('id', 'name');
        }])->first();

    if(!$product) abort(404);

    $product_details = ProductDetail::where([['product_id', $id], ['import_quantity', '>', 0]])->with([
      'product_images' => function ($query) {
        $query->select('id', 'product_detail_id', 'image_name');
      }
    ])->select('id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->get();

    $suggest_products = Product::select('id','name', 'image', 'rate')
    ->whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->where([['producer_id', $product->producer_id], ['id', '<>', $id]])
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->limit(3)->get();

    $product_votes = ProductVote::whereHas('user', function (Builder $query) {
      $query->where([['active', true], ['admin', false]]);
    })->where('product_id', $id)->with(['user' => function($query) {
      $query->select('id', 'name', 'avatar_image');
    }])->latest()->get();

    return view('pages.product')->with(['data' => ['advertises' => $advertises, 'product' => $product, 'product_details' => $product_details, 'suggest_products' => $suggest_products, 'product_votes' => $product_votes]]);
  }

  /**
   * Chức năng thêm hoặc cập nhật một sản phẩm bình chọn và tính tỷ lệ trung bình cho sản phẩm, sau đó
   * cập nhật tỷ lệ của sản phẩm và trả về thông báo thành công.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu, chứa dữ liệu được gửi
   * trong yêu cầu HTTP. Nó được sử dụng để truy xuất các giá trị của user_id, product_id, nội dung và
   * tham số tỷ lệ được gửi trong yêu cầu.
   *
   * @return chuyển hướng trở lại trang trước với thông báo phiên chứa cảnh báo thành công
   * kèm theo tiêu đề và nội dung tin nhắn.
   */
  public function addVote(Request $request) {
    $vote = ProductVote::updateOrCreate(
        ['user_id' => $request->user_id, 'product_id' => $request->product_id],
        ['content' => $request->content, 'rate' => $request->rate]
    );
    $rate = ProductVote::where('product_id', $request->product_id)->avg('rate');

    $product = Product::where('id', $request->product_id)->first();
    $product->rate = $rate;
    $product->save();

    return back()->with(['vote_alert' => [
        'type' => 'success',
        'title' => 'Đã Gửi Đánh Giá',
        'content' => 'Cảm ơn bạn đã đóng góp về sản phẩm này. Chúng tôi luôn luôn trân trong những đóng góp của bạn.'
    ]]);
  }
}
