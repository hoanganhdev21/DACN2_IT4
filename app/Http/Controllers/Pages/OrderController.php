<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;
use App\Models\Advertise;

/* Lớp OrderController xử lý hiển thị đơn đặt hàng cho người dùng được xác thực và chuyển hướng
người dùng trái phép vào các trang thích hợp. */
class OrderController extends Controller
{
  /**
   * Chức năng này kiểm tra xem người dùng có đăng nhập và không phải là quản trị viên hay không, truy xuất đơn đặt hàng của họ và
   * hiển thị chúng cùng với một số quảng cáo hoặc chuyển hướng chúng đến trang đăng nhập nếu không
   * đăng nhập.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request, mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
   * URL, tiêu đề và bất kỳ dữ liệu nào được gửi trong nội dung yêu cầu. Nó thường được sử dụng để lấy dữ liệu đầu vào
   * từ yêu cầu, chẳng hạn như dữ liệu biểu mẫu hoặc truy vấn
   *
   * @return chế độ xem với dữ liệu chứa đơn đặt hàng và quảng cáo nếu người dùng đăng nhập với tư cách là
   * người dùng thường xuyên và có đơn đặt hàng. Nếu người dùng đăng nhập với tư cách quản trị viên, họ sẽ được chuyển hướng đến
   * bảng điều khiển quản trị với thông báo cảnh báo. Nếu người dùng chưa đăng nhập, họ sẽ được chuyển hướng đến
   * trang đăng nhập có thông báo cảnh báo.
   */
  public function index(Request $request)
  {
    if(Auth::check() && Auth::user()->admin == 0) {
      $advertises = Advertise::where([
        ['start_date', '<=', date('Y-m-d')],
        ['end_date', '>=', date('Y-m-d')],
        ['at_home_page', '=', false]
      ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

      $orders = Order::where('user_id', Auth::user()->id)->with([
        'payment_method' => function ($query) {
          $query->select('id', 'name');
        },
        'order_details' => function($query) {
          $query->select('id', 'order_id', 'quantity', 'price');
        }
      ])->orderBy('id', 'ASC')->get();
      if($orders->isNotEmpty()) {
        return view('pages.orders')->with('data',['orders' => $orders, 'advertises' => $advertises]);
      } else {
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'info',
          'title' => 'Thông Báo',
          'content' => 'Bạn không có đơn hàng nào. Hãy mua hàng để thực hiện chức năng này!'
        ]]);
      }
    } else if(Auth::check()) {
      return redirect()->route('admin.dashboard')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn không có quyền truy cập vào trang này!'
      ]]);
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }

  /**
   * Chức năng này hiển thị một đơn đặt hàng với các chi tiết của nó và các quảng cáo có liên quan, nhưng chỉ khi người dùng là
   * đã đăng nhập và có quyền thích hợp.
   *
   * @param id Tham số `` là định danh của đơn hàng cần hiển thị. Nó là
   * được sử dụng để truy xuất chi tiết đơn đặt hàng từ cơ sở dữ liệu.
   *
   * @return chế độ xem có tên 'pages.order' với dữ liệu chứa đơn đặt hàng và danh sách quảng cáo.
   * Đơn hàng được lấy từ cơ sở dữ liệu dựa trên ID được cung cấp và bao gồm các chi tiết như
   * phương thức thanh toán, thông tin người dùng và chi tiết đơn hàng. Chức năng này cũng bao gồm xác thực
   * kiểm tra để đảm bảo rằng chỉ những người dùng được ủy quyền mới có thể truy cập trang. Nếu người dùng chưa đăng nhập,
   * họ sẽ được chuyển hướng đến đăng nhập
   */
  public function show($id)
  {
    if(Auth::check() && Auth::user()->admin == 0) {
      $advertises = Advertise::where([
        ['start_date', '<=', date('Y-m-d')],
        ['end_date', '>=', date('Y-m-d')],
        ['at_home_page', '=', false]
      ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

      $order = Order::where('id', $id)->with([
        'payment_method' => function($query) {
          $query->select('id', 'name');
        },
        'user' => function($query) {
          $query->select('id', 'name',  'email', 'phone', 'address');
        },
        'order_details' => function($query) {
          $query->select('id', 'order_id', 'product_detail_id', 'quantity', 'price')
          ->with([
            'product_detail' => function ($query) {
              $query->select('id', 'product_id', 'color')
              ->with([
                'product' => function ($query) {
                  $query->select('id', 'name', 'image', 'sku_code');
                }
              ]);
            }
          ]);
        }
      ])->first();

      if(!$order) abort(404);

      if(Auth::user()->id != $order->user_id) {
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền truy cập vào trang này!'
        ]]);
      } else {
        return view('pages.order')->with('data',['order' => $order, 'advertises' => $advertises]);
      }

    } else if(Auth::check()) {
      return redirect()->route('admin.dashboard')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn không có quyền truy cập vào trang này!'
      ]]);
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }
}
