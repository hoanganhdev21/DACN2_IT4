<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use App\Models\User;
use App\Models\Post;
use App\Models\Product;
use App\Models\Order;

/* Lớp DashboardController truy xuất và trả về số lượng người dùng đang hoạt động, bài đăng, có sẵn
sản phẩm và đơn đặt hàng đã hoàn thành cho chế độ xem bảng điều khiển của quản trị viên. */
class DashboardController extends Controller
{
  public function index() {

    $count['user'] = User::where([['active', true], ['admin', false]])->count();
    $count['post'] = Post::count();
    $count['product'] = Product::whereHas('product_details', function (Builder $query) {
      $query->where('quantity', '>', 0);
    })->count();
    $count['order'] = Order::where('status', true)->count();
    return view('admin.index')->with(['count' => $count]);
  }
}
