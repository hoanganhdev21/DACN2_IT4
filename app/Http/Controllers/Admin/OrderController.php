<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
// Controller bảng điều khiển
/* Lớp OrderController lấy và hiển thị thông tin về các đơn hàng, cập nhật trạng thái của chúng
dựa trên các hành động và trả về các dạng xem để hiển thị. */
class OrderController extends Controller
{
  /**
   * This function retrieves a list of orders with specific fields and relationships, excluding those with a status of 0, and returns them to a view for display.
   * 
   * @return a view called 'admin.order.index' with a variable called 'orders', which contains a collection of orders with selected attributes and relationships loaded. The orders are filtered by status not equal to 0 and sorted by the latest created_at date.
   */
  public function index()
  {
    $orders = Order::select('id', 'user_id','status', 'payment_method_id','status', 'order_code', 'name', 'email', 'phone', 'created_at')->where('status', '<>', 0)->with([
        'user' => function ($query) {
          $query->select('id', 'name');
        },
        'payment_method' => function ($query) {
          $query->select('id', 'name');
        }
      ])->latest()->get();
    return view('admin.order.index')->with('orders', $orders);
  }

  /**
   * This function retrieves and displays information about a specific order, including user details,payment method, and order details with product information.
   * 
   * @param id The parameter `` is the unique identifier of the order that needs to be displayed. It is used to retrieve the order details from the database.
   * 
   * @return a view called 'admin.order.show' with the variable , which contains information about a specific order including the user, payment method, and order details. If the order does not exist, it will return a 404 error.
   */
  public function show($id)
  {
    $order = Order::select('id', 'user_id', 'payment_method_id', 'order_code', 'name', 'email', 'phone', 'address', 'created_at')->where([['status', '<>', 0], ['id', $id]])->with([
        'user' => function ($query) {
          $query->select('id', 'name', 'email', 'phone', 'address');
        },
        'payment_method' => function ($query) {
          $query->select('id', 'name', 'describe');
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
    return view('admin.order.show')->with('order', $order);
  }

  /**
   * The function updates the status of an order based on the action provided and redirects back to the previous page.
   * 
   * @param action The parameter "action" is a string that represents the action to be performed on an order. It can have one of the following values: "process", "success", or "cancel".
   * @param id The "id" parameter is the identifier of the order that needs to be processed, marked as successful, or cancelled. It is used to retrieve the order from the database using the "find" method of the "Order" model.
   * 
   * @return a redirect back to the previous page.
   */
  public function actionTransaction($action,$id){
    $orderAction = Order::find($id);
    if($orderAction){
      switch ($action) {
        case 'process':
          $orderAction->status= 2;
          break;
        case 'success':
          $orderAction->status= 3;
          break;
        case 'cancel':
          $orderAction->status= -1;
          break;
      }
      $orderAction->save();
    }
    return redirect()->back();
  }
}
