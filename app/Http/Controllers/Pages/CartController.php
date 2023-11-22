<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\ProductDetail;
use App\Models\Cart;
use App\Models\Advertise;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderDetail;
use App\NL_Checkout;

/* Lớp CartController thêm sản phẩm vào giỏ hàng và trả về phản hồi JSON. */
class CartController extends Controller
{
  public function addCart(Request $request) {

    $product = ProductDetail::where('id',$request->id)
    ->with(['product' => function($query) {
      $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
    }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();

    if(!$product) {
      $data['msg'] = 'Product Not Found!';
      return response()->json($data, 404);
    }

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->add($product, $product->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $data['msg'] = "Thêm giỏ hàng thành công";
    $data['url'] = route('home_page');
    $data['response'] = Session::get('cart');

    return response()->json($data, 200);
  }

  /**
   * Hàm xóa một mặt hàng khỏi giỏ hàng và trả về phản hồi JSON thành công hoặc
   * thông báo lỗi.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa dữ liệu được gửi
   * bởi máy khách trong yêu cầu HTTP. Nó có thể được sử dụng để truy xuất dữ liệu đầu vào, tệp, cookie, tiêu đề,
   * và các thông tin khác liên quan đến yêu cầu. Trong chức năng này, nó được sử dụng để lấy ID của
   * sản phẩm cần
   *
   * @return Hàm này đang trả về phản hồi JSON có thông báo, URL và giỏ hàng được cập nhật
   * dữ liệu. Nếu sản phẩm cần xóa không tồn tại trong giỏ hàng, nó sẽ trả về lỗi 404. Nếu
   * sản phẩm được xóa thành công, nó trả về mã trạng thái 200 thành công.
   */
  public function removeCart(Request $request) {

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);

    if(!$cart->remove($request->id)) {
      $data['msg'] = 'Sản Phẩm không tồn tại!';
      return response()->json($data, 404);
    } else {
      Session::put('cart', $cart);

      $data['msg'] = "Xóa sản phẩm thành công";
      $data['url'] = route('home_page');
      $data['response'] = Session::get('cart');

      return response()->json($data, 200);
    }
  }

  /**
   * Hàm cập nhật số lượng mặt hàng trong giỏ hàng và trả về phản hồi JSON với
   * thông tin mục cập nhật.
   *
   * @param Yêu cầu yêu cầu là một đối tượng của lớp Yêu cầu chứa dữ liệu được gửi bởi
   * ứng dụng khách trong yêu cầu HTTP. Nó được sử dụng để lấy dữ liệu từ yêu cầu như dữ liệu biểu mẫu,
   * tham số truy vấn và tiêu đề yêu cầu. Trong chức năng cụ thể này, nó được sử dụng để truy xuất
   * ID sản phẩm và số lượng mà người dùng
   *
   * @return Hàm này trả về phản hồi JSON với dữ liệu về mặt hàng trong giỏ hàng được cập nhật,
   * bao gồm ID, số lượng, giá, giá bán, tổng giá và số lượng của tất cả các mặt hàng trong giỏ hàng,
   * cũng như số lượng tối đa có sẵn cho mặt hàng đó. Nếu cập nhật không thành công do
   * vượt quá số lượng có sẵn trong kho, phản hồi JSON có thông báo lỗi và mã trạng thái
   * 412 được trả lại.
   */
  public function updateCart(Request $request) {
    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->updateItem($request->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $response = array(
      'id' => $request->id,
      'qty' => $cart->items[$request->id]['qty'],
      'price' => $cart->items[$request->id]['price'],
      'salePrice' => $cart->items[$request->id]['item']->sale_price,
      'totalPrice' => $cart->totalPrice,
      'totalQty' => $cart->totalQty,
      'maxQty'  =>  $cart->items[$request->id]['item']->quantity
    );
    $data['response'] = $response;
    return response()->json($data, 200);
  }

  /**
   * Hàm cập nhật số lượng mặt hàng trong giỏ hàng và trả về phản hồi JSON với
   * thông tin mục cập nhật.
   *
   * @param Yêu cầu yêu cầu là một đối tượng của lớp Yêu cầu chứa dữ liệu được gửi bởi
   * ứng dụng khách trong yêu cầu HTTP. Nó được sử dụng để lấy các giá trị của các tham số và dữ liệu được gửi
   * trong yêu cầu. Trong chức năng này, nó được sử dụng để lấy id và số lượng của mặt hàng sẽ được cập nhật trong
   * giỏ hàng
   *
   * @return Hàm này trả về phản hồi JSON với thông tin cập nhật của mặt hàng trong giỏ hàng,
   * bao gồm ID, số lượng, giá, tổng giá, tổng số lượng và số lượng tối đa có sẵn. Nếu như
   * cập nhật không thành công do vượt quá số lượng có sẵn trong kho, nó sẽ trả về
   * Phản hồi JSON có thông báo lỗi và mã trạng thái là 412.
   */
  public function updateMiniCart(Request $request) {
    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->updateItem($request->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $response = array(
      'id' => $request->id,
      'qty' => $cart->items[$request->id]['qty'],
      'price' => $cart->items[$request->id]['price'],
      'totalPrice' => $cart->totalPrice,
      'totalQty' => $cart->totalQty,
      'maxQty'  =>  $cart->items[$request->id]['item']->quantity
    );
    $data['response'] = $response;
    return response()->json($data, 200);
  }

 /**
  * Chức năng lấy dữ liệu giỏ hàng từ phiên và hiển thị nó cùng với danh sách
  * quảng cáo.
  *
  * @return chế độ xem có tên 'pages.cart' với giỏ hàng và quảng cáo dữ liệu được truyền dưới dạng biến cho
  * xem.
  */
  public function showCart() {

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);

    return view('pages.cart')->with(['cart' => $cart, 'advertises' => $advertises]);
  }

  /**
   * Chức năng kiểm tra xem người dùng đã đăng nhập và không phải là quản trị viên, sau đó hiển thị trang thanh toán
   * với phương thức thanh toán và sản phẩm đã chọn từ giỏ hàng hoặc từ một sản phẩm
   * mua.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu hiện tại, chẳng hạn như yêu cầu
   * phương thức, tiêu đề, tham số và cookie. Nó được sử dụng để lấy dữ liệu từ yêu cầu và chuyển
   * dữ liệu vào dạng xem. Trong chức năng cụ thể này, nó là
   *
   * @return chế độ xem có tên 'pages.checkout' với các biến sau: 'cart', 'payment_methods' và
   * 'mua_phương_thức'. Các biến cụ thể được chuyển đến dạng xem phụ thuộc vào các điều kiện trong if-else
   * các câu lệnh. Nếu người dùng chưa đăng nhập, họ sẽ được chuyển hướng đến trang đăng nhập với một cảnh báo
   * tin nhắn. Nếu người dùng là quản trị viên, họ sẽ được chuyển hướng đến
   */
  public function showCheckout(Request $request)
  {
    if(Auth::check() && !Auth::user()->admin) {
      if($request->has('type') && $request->type == 'buy_now') {
        $payment_methods = PaymentMethod::select('id', 'name', 'describe')->get();
        $product = ProductDetail::where('id',$request->id)
          ->with(['product' => function($query) {
            $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
          }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();
        $cart = new Cart(NULL);
        if(!$cart->add($product, $product->id, $request->qty)) {
          return back()->with(['alert' => [
              'type' => 'warning',
              'title' => 'Thông Báo',
              'content' => 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!'
          ]]);
        }
        return view('pages.checkout')->with(['cart' => $cart, 'payment_methods' => $payment_methods, 'buy_method' =>$request->type]);
      } elseif($request->has('type') && $request->type == 'buy_cart') {

        $payment_methods = PaymentMethod::select('id', 'name', 'describe')->get();
        $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
        $cart = new Cart($oldCart);
        $cart->update();
        Session::put('cart', $cart);
        return view('pages.checkout')->with(['cart' => $cart, 'payment_methods' => $payment_methods, 'buy_method' =>$request->type]);
      }
    } elseif(Auth::check() && Auth::user()->admin) {
      return redirect()->route('home_page')->with(['alert' => [
        'type' => 'error',
        'title' => 'Thông Báo',
        'content' => 'Bạn không có quyền truy cập vào trang này!'
      ]]);
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'info',
        'title' => 'Thông Báo',
        'content' => 'Bạn hãy đăng nhập để mua hàng!'
      ]]);
    }
  }

  /**
   * Chức năng này xử lý quy trình thanh toán cho đơn hàng của người dùng, bao gồm tạo đơn hàng và
   * chi tiết đơn hàng, cập nhật số lượng sản phẩm và tạo URL thanh toán cho thanh toán trực tuyến.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu, chứa dữ liệu
   * đã gửi trong yêu cầu HTTP. Nó được sử dụng để lấy dữ liệu từ các trường biểu mẫu và URL
   * thông số.
   *
   * @return Hàm này không trả về bất cứ thứ gì một cách rõ ràng, nhưng nó chuyển hướng người dùng tới một URL cho
   * xử lý thanh toán bằng cổng thanh toán NgânLượng.
   */
  public function payment(Request $request) {
    $payment_method = PaymentMethod::select('id', 'name')->where('id', $request->payment_method)->first();
    if(Str::contains($payment_method->name, 'COD')) {
      if($request->buy_method == 'buy_now'){
        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 1;
        $order->save();

        $order_details = new OrderDetail;
        $order_details->order_id = $order->id;
        $order_details->product_detail_id = $request->product_id;
        $order_details->quantity = $request->totalQty;
        $order_details->price = $request->price;
        $order_details->save();

        $product = ProductDetail::find($request->product_id);
        $product->quantity = $product->quantity - $request->totalQty;
        $product->save();

        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Mua hàng thành công',
          'content' => 'Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi. Sản phẩm của bạn sẽ được chuyển đến trong thời gian sớm nhất.'
        ]]);
      } elseif ($request->buy_method == 'buy_cart') {
        $cart = Session::get('cart');

        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 1;
        $order->save();

        foreach ($cart->items as $key => $item) {
          $order_details = new OrderDetail;
          $order_details->order_id = $order->id;
          $order_details->product_detail_id = $item['item']->id;
          $order_details->quantity = $item['qty'];
          $order_details->price = $item['price'];
          $order_details->save();

          $product = ProductDetail::find($item['item']->id);
          $product->quantity = $product->quantity - $item['qty'];
          $product->save();
        }
        Session::forget('cart');
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Mua hàng thành công',
          'content' => 'Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi. Sản phẩm của bạn sẽ được chuyển đến trong thời gian sớm nhất.'
        ]]);
      }
    } elseif(Str::contains($payment_method->name, 'Online Payment')) {
      if($request->buy_method == 'buy_now'){
        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 0;
        $order->save();

        $order_details = new OrderDetail;
        $order_details->order_id = $order->id;
        $order_details->product_detail_id = $request->product_id;
        $order_details->quantity = $request->totalQty;
        $order_details->price = $request->price;
        $order_details->save();

        // Tạo url thanh toán
        $receiver = env('RECEIVER');
        $order_code = $order->order_code;
        $return_url = route('payment_response');
        $cancel_url = route('payment_response');
        $notify_url = route('payment_response');
        $transaction_info = $order->id;
        $currency = "vnd";
        $quantity = $request->totalQty;
        $price = $order_details->price * $order_details->quantity;
        $tax =0;
        $discount =0;
        $fee_cal =0;
        $fee_shipping =0;
        $order_description ="Thanh toán đơn hàng ".config('app.name');
        $buyer_info = $request->name."*|*".$request->email."*|*".$request->phone."*|*".$request->address;
        $affiliate_code = "";

        $nl= new NL_Checkout();
        $nl->nganluong_url = env('NGANLUONG_URL');
        $nl->merchant_site_code = env('MERCHANT_ID');
        $nl->secure_pass = env('MERCHANT_PASS');

        $url= $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);
        $url .='&cancel_url='. $cancel_url . '&notify_url='.$notify_url;

        return redirect()->away($url);
      } elseif ($request->buy_method == 'buy_cart') {
        $cart = Session::get('cart');

        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 0;
        $order->save();

        foreach ($cart->items as $key => $item) {
          $order_details = new OrderDetail;
          $order_details->order_id = $order->id;
          $order_details->product_detail_id = $item['item']->id;
          $order_details->quantity = $item['qty'];
          $order_details->price = $item['price'];
          $order_details->save();
        }

        // Tạo url thanh toán
        $receiver = env('RECEIVER');
        $order_code = $order->order_code;
        $return_url = route('payment_response');
        $cancel_url = route('payment_response');
        $notify_url = route('payment_response');
        $transaction_info = $order->id;
        $currency = "vnd";
        $quantity = $cart->totalQty;
        $price = $cart->totalPrice;
        $tax =0;
        $discount =0;
        $fee_cal =0;
        $fee_shipping =0;
        $order_description ="Thanh toán đơn hàng ".config('app.name');
        $buyer_info = $request->name."*|*".$request->email."*|*".$request->phone."*|*".$request->address;
        $affiliate_code = "";

        $nl= new NL_Checkout();
        $nl->nganluong_url = env('NGANLUONG_URL');
        $nl->merchant_site_code = env('MERCHANT_ID');
        $nl->secure_pass = env('MERCHANT_PASS');

        $url= $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);
        $url .='&cancel_url='. $cancel_url . '&notify_url='.$notify_url;

        Session::forget('cart');
        return redirect()->away($url);
      }
    }
  }

  /**
   * Chức năng này xử lý phản hồi từ cổng thanh toán và cập nhật trạng thái đơn hàng
   * cho phù hợp.
   *
   * @param Yêu cầu yêu cầu là một đối tượng chứa dữ liệu được gửi trong yêu cầu HTTP. Nó
   * là một thể hiện của lớp Yêu cầu trong Laravel. Nó có thể được sử dụng để lấy dữ liệu từ yêu cầu,
   * chẳng hạn như dữ liệu biểu mẫu, tham số truy vấn và tiêu đề. Trong chức năng này, nó được sử dụng để truy xuất
   * thông tin thanh toán được gửi
   *
   * @return phản hồi chuyển hướng đến trang chủ với thông báo cảnh báo. Loại tin nhắn cảnh báo
   * phụ thuộc vào kết quả của quá trình xác minh thanh toán. Nếu thanh toán được xác minh
   * thành công, thông báo thành công được trả về. Nếu không, một thông báo lỗi được trả về với
   * văn bản lỗi. Nếu ID thanh toán không được cung cấp trong yêu cầu, thông báo lỗi sẽ được trả về
   * chỉ ra rằng thanh toán không thành công.
   */
  public function responsePayment(Request $request) {
    if ($request->filled('payment_id')) {

      // Lấy các tham số để chuyển sang Ngânlượng thanh toán:
      $transaction_info =$request->transaction_info;
      $order_code =$request->order_code;
      $price =$request->price;
      $payment_id =$request->payment_id;
      $payment_type =$request->payment_type;
      $error_text =$request->error_text;
      $secure_code =$request->secure_code;

      //Khai báo đối tượng của lớp NL_Checkout
      $nl= new NL_Checkout();
      $nl->merchant_site_code = env('MERCHANT_ID');
      $nl->secure_pass = env('MERCHANT_PASS');

      //Tạo link thanh toán đến nganluong.vn
      $checkpay= $nl->verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code);

      if ($checkpay) {
        $order = Order::where([['id', $transaction_info],['order_code', $order_code]])->first();
        $order->status = 1;
        $order->save();

        foreach ($order->order_details as $order_detail) {
          $product_detail = ProductDetail::where('id', $order_detail->product_detail_id)->first();
          $product_detail->quantity = $product_detail->quantity - $order_detail->quantity;
          $product_detail->save();
        }
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Thanh toán thành công!',
          'content' => 'Cảm ơn bạn đã tin tưởng và lựa chọn chúng tối.'
        ]]);
      }else{
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'error',
          'title' => 'Thanh toán không thành công!',
          'content' => $error_text
        ]]);
      }
    } else {
      return redirect()->route('home_page')->with(['alert' => [
        'type' => 'error',
        'title' => 'Thanh toán không thành công!',
        'content' => 'Bạn đã hủy hoặc đã xẩy ra lỗi trong quá trình thanh toán.'
      ]]);
    }
  }

  
/**
   * Hàm PHP này tạo URL thanh toán cho cổng thanh toán VNPAY với nhiều tham số khác nhau.
   */
  public function vnpay_payment(){
    $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl = "http://127.0.0.1:8000/checkout";
    $vnp_TmnCode = "5V4XT3RE";//Mã website tại VNPAY 
    $vnp_HashSecret = "BKXHBRZUBGCJHYRMVQWRJOKBRFCNGMSW"; //Chuỗi bí mật

    $vnp_TxnRef = '12347'; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
    $vnp_OrderInfo = 'Thanh toán đơn hàng test';
    $vnp_OrderType = 'billpayment';
    $vnp_Amount = 20000 * 100;
    $vnp_Locale = 'vn';
    $vnp_BankCode = 'NCB';
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
    //Add Params of 2.0.1 Version
    // $vnp_ExpireDate = $_POST['txtexpire'];
    //Billing
    // $vnp_Bill_Mobile = $_POST['txt_billing_mobile'];
    // $vnp_Bill_Email = $_POST['txt_billing_email'];
    // $fullName = trim($_POST['txt_billing_fullname']);
    // if (isset($fullName) && trim($fullName) != '') {
    //     $name = explode(' ', $fullName);
    //     $vnp_Bill_FirstName = array_shift($name);
    //     $vnp_Bill_LastName = array_pop($name);
    // }
    // $vnp_Bill_Address=$_POST['txt_inv_addr1'];
    // $vnp_Bill_City=$_POST['txt_bill_city'];
    // $vnp_Bill_Country=$_POST['txt_bill_country'];
    // $vnp_Bill_State=$_POST['txt_bill_state'];
    // // Invoice
    // $vnp_Inv_Phone=$_POST['txt_inv_mobile'];
    // $vnp_Inv_Email=$_POST['txt_inv_email'];
    // $vnp_Inv_Customer=$_POST['txt_inv_customer'];
    // $vnp_Inv_Address=$_POST['txt_inv_addr1'];
    // $vnp_Inv_Company=$_POST['txt_inv_company'];
    // $vnp_Inv_Taxcode=$_POST['txt_inv_taxcode'];
    // $vnp_Inv_Type=$_POST['cbo_inv_type'];
    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
        // "vnp_ExpireDate"=>$vnp_ExpireDate
        // "vnp_Bill_Mobile"=>$vnp_Bill_Mobile,
        // "vnp_Bill_Email"=>$vnp_Bill_Email,
        // "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
        // "vnp_Bill_LastName"=>$vnp_Bill_LastName,
        // "vnp_Bill_Address"=>$vnp_Bill_Address,
        // "vnp_Bill_City"=>$vnp_Bill_City,
        // "vnp_Bill_Country"=>$vnp_Bill_Country,
        // "vnp_Inv_Phone"=>$vnp_Inv_Phone,
        // "vnp_Inv_Email"=>$vnp_Inv_Email,
        // "vnp_Inv_Customer"=>$vnp_Inv_Customer,
        // "vnp_Inv_Address"=>$vnp_Inv_Address,
        // "vnp_Inv_Company"=>$vnp_Inv_Company,
        // "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
        // "vnp_Inv_Type"=>$vnp_Inv_Type
    );

    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        $inputData['vnp_BankCode'] = $vnp_BankCode;
    }
    if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
        $inputData['vnp_Bill_State'] = $vnp_Bill_State;
    }

    //var_dump($inputData);
    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $vnp_Url = $vnp_Url . "?" . $query;
    if (isset($vnp_HashSecret)) {
        $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }
    $returnData = array('code' => '00'
    , 'message' => 'success'
    , 'data' => $vnp_Url);
    if (isset($_POST['redirect'])) {
        header('Location: ' . $vnp_Url);
        die();
    } else {
        echo json_encode($returnData);
    }
  }
}
