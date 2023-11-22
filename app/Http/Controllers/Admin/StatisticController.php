<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Producer;
// controller số liệu thống kê:
class StatisticController extends Controller
{
  
  /**
   * Hàm PHP này tạo số liệu thống kê cho một tháng nhất định, bao gồm tổng doanh thu, tổng lợi nhuận và chi tiết sản phẩm, đồng thời sắp xếp dữ liệu theo ngày và nhà sản xuất.
   * 
   * @return một chế độ xem có tên là 'admin.statistic.index' với một biến có tên là 'data' chứa nhiều
   * dữ liệu liên quan đến thống kê đơn đặt hàng và sản phẩm cho tháng hiện tại.
   */
  public function index()
  {
    $carbon = new Carbon('first day of this month');

    $count_products = 0;
    $total_revenue = 0;
    $total_profit = 0;

    for($i = 0; $i < $carbon->daysInMonth; $i++) {

      $date = $carbon->copy()->addDay($i)->format('d/m/Y');

      $data['labels'][] = $date;

      $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
        ->whereDate('created_at', $carbon->copy()->addDay($i)->format('Y-m-d'))
        ->whereHas('order', function (Builder $query) {
          $query->where('status', '=', 3);
        })->with([
          'product_detail' => function($query) {
            $query->select('id', 'import_price');
          }
        ])->get();

      $revenue = 0;
      $profit = 0;

      foreach ($order_details as $order_detail) {
        $revenue = $revenue + $order_detail->price * $order_detail->quantity;
        $profit = $profit + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
        $count_products = $count_products + $order_detail->quantity;
      }

      $total_revenue = $total_revenue + $revenue;
      $total_profit = $total_profit + $profit;
      $data['revenues'][] = $revenue;
    }

    $data['count_products'] = $count_products;
    $data['total_revenue'] = $total_revenue;
    $data['total_profit'] = $total_profit;
    $data['count_orders'] = Order::where('status', '=', 3)
      ->whereYear('created_at', $carbon->year)
      ->whereMonth('created_at', $carbon->month)->count();

    $order_details = OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')->whereYear('created_at', $carbon->year)->whereMonth('created_at', $carbon->month)
      ->whereHas('order', function (Builder $query) {
        $query->where('status', '=', 3);
      })->with([
        'order' => function($query) {
          $query->select('id', 'order_code');
        },
        'product_detail' =>function($query) {
          $query->select('id', 'product_id', 'color', 'import_price')->with([
            'product' => function($query) {
              $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                'producer' => function($query) {
                  $query->select('id', 'name');
                }
              ]);
            }
          ]);
        }
      ])->latest()->get();

    $data['order_details'] = $order_details;

    $producers = Producer::select('name')->has('products')->get();

    foreach ($producers as $producer) {
      $data['producer'][$producer->name]['quantity'] = 0;
      $data['producer'][$producer->name]['revenue'] = 0;
      $data['producer'][$producer->name]['profit'] = 0;
    }

    foreach ($order_details as $order_detail) {
      $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] = $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] + $order_detail->quantity;

      $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] = $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] + $order_detail->quantity * $order_detail->price;

      $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] = $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
    }

    return view('admin.statistic.index')->with('data', $data);
  }

  public function edit(Request $request)
  {
    if($request->month != null && $request->year == null) {
      $carbon = Carbon::createFromDate(date('Y'), $request->month, 1);

      $count_products = 0;
      $total_revenue = 0;
      $total_profit = 0;

      for($i = 0; $i < $carbon->daysInMonth; $i++) {

        $date = $carbon->copy()->addDay($i)->format('d/m/Y');

        $data['labels'][] = $date;

        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
          ->whereDate('created_at', $carbon->copy()->addDay($i)->format('Y-m-d'))
          ->whereHas('order', function (Builder $query) {
            $query->where('status', '=', 3);
          })->with([
            'product_detail' => function($query) {
              $query->select('id', 'import_price');
            }
          ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
          $revenue = $revenue + $order_detail->price * $order_detail->quantity;
          $profit = $profit + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
          $count_products = $count_products + $order_detail->quantity;
        }

        $total_revenue = $total_revenue + $revenue;
        $total_profit = $total_profit + $profit;
        $data['revenues'][] = $revenue;
      }

      $data['count_products'] = $count_products;
      $data['total_revenue'] = $total_revenue;
      $data['total_profit'] = $total_profit;
      $data['count_orders'] = Order::where('status', '=', 3)
        ->whereYear('created_at', $carbon->year)
        ->whereMonth('created_at', $carbon->month)->count();

      $order_details = OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')->whereYear('created_at', $carbon->year)->whereMonth('created_at', $carbon->month)
        ->whereHas('order', function (Builder $query) {
          $query->where('status', '=', 3);
        })->with([
          'order' => function($query) {
            $query->select('id', 'order_code');
          },
          'product_detail' =>function($query) {
            $query->select('id', 'product_id', 'color', 'import_price')->with([
              'product' => function($query) {
                $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                  'producer' => function($query) {
                    $query->select('id', 'name');
                  }
                ]);
              }
            ]);
          }
        ])->latest()->get();

      $data['order_details'] = $order_details;

      $producers = Producer::select('name')->has('products')->get();

      foreach ($producers as $producer) {
        $data['producer'][$producer->name]['quantity'] = 0;
        $data['producer'][$producer->name]['revenue'] = 0;
        $data['producer'][$producer->name]['profit'] = 0;
      }

      foreach ($order_details as $order_detail) {
        $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] = $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] + $order_detail->quantity;

        $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] = $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] + $order_detail->quantity * $order_detail->price;

        $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] = $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
      }
      $data['text']['title1'] = 'Biểu Đồ Kinh Doanh Tháng '.$request->month.' Năm '.date('Y');
      $data['text']['title2'] = 'Danh Sách Sản Phẩm Xuất Kho Tháng '.$request->month.' Năm '.date('Y');
      $data['text']['revenue'] = 'DOANH THU THÁNG';
      $data['text']['profit'] = 'LỢI NHUẬN THÁNG';

    } elseif($request->month != null && $request->year != null) {
      $carbon = Carbon::createFromDate($request->year, $request->month, 1);

      $count_products = 0;
      $total_revenue = 0;
      $total_profit = 0;

      for($i = 0; $i < $carbon->daysInMonth; $i++) {

        $date = $carbon->copy()->addDay($i)->format('d/m/Y');

        $data['labels'][] = $date;

        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
          ->whereDate('created_at', $carbon->copy()->addDay($i)->format('Y-m-d'))
          ->whereHas('order', function (Builder $query) {
            $query->where('status', '=', 3);
          })->with([
            'product_detail' => function($query) {
              $query->select('id', 'import_price');
            }
          ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
          $revenue = $revenue + $order_detail->price * $order_detail->quantity;
          $profit = $profit + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
          $count_products = $count_products + $order_detail->quantity;
        }

        $total_revenue = $total_revenue + $revenue;
        $total_profit = $total_profit + $profit;
        $data['revenues'][] = $revenue;
      }

      $data['count_products'] = $count_products;
      $data['total_revenue'] = $total_revenue;
      $data['total_profit'] = $total_profit;
      $data['count_orders'] = Order::where('status', '=', 3)
        ->whereYear('created_at', $carbon->year)
        ->whereMonth('created_at', $carbon->month)->count();

      $order_details = OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')->whereYear('created_at', $carbon->year)->whereMonth('created_at', $carbon->month)
        ->whereHas('order', function (Builder $query) {
          $query->where('status', '=', 3);
        })->with([
          'order' => function($query) {
            $query->select('id', 'order_code');
          },
          'product_detail' =>function($query) {
            $query->select('id', 'product_id', 'color', 'import_price')->with([
              'product' => function($query) {
                $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                  'producer' => function($query) {
                    $query->select('id', 'name');
                  }
                ]);
              }
            ]);
          }
        ])->latest()->get();

      $data['order_details'] = $order_details;

     /* The above code is written in PHP and it is selecting the names of all the producers who have at
     least one product. It is using the Eloquent ORM to query the database and retrieve the required
     data. The retrieved data is stored in the  variable. */
      $producers = Producer::select('name')->has('products')->get();

      foreach ($producers as $producer) {
        $data['producer'][$producer->name]['quantity'] = 0;
        $data['producer'][$producer->name]['revenue'] = 0;
        $data['producer'][$producer->name]['profit'] = 0;
      }

      foreach ($order_details as $order_detail) {
        $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] = $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] + $order_detail->quantity;

        $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] = $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] + $order_detail->quantity * $order_detail->price;

        $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] = $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
      }
      $data['text']['title1'] = 'Biểu Đồ Kinh Doanh Tháng '.$request->month.' Năm '.$request->year;
      $data['text']['title2'] = 'Danh Sách Sản Phẩm Xuất Kho Tháng '.$request->month.' Năm '.$request->year;
      $data['text']['revenue'] = 'DOANH THU THÁNG';
      $data['text']['profit'] = 'LỢI NHUẬN THÁNG';
    } elseif($request->month == null && $request->year == null) {
      $carbon = new Carbon('first day of this month');

      $count_products = 0;
      $total_revenue = 0;
      $total_profit = 0;

      for($i = 0; $i < $carbon->daysInMonth; $i++) {

        $date = $carbon->copy()->addDay($i)->format('d/m/Y');

        $data['labels'][] = $date;

        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
          ->whereDate('created_at', $carbon->copy()->addDay($i)->format('Y-m-d'))
          ->whereHas('order', function (Builder $query) {
            $query->where('status', '=', 3);
          })->with([
            'product_detail' => function($query) {
              $query->select('id', 'import_price');
            }
          ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
          $revenue = $revenue + $order_detail->price * $order_detail->quantity;
          $profit = $profit + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
          $count_products = $count_products + $order_detail->quantity;
        }

        $total_revenue = $total_revenue + $revenue;
        $total_profit = $total_profit + $profit;
        $data['revenues'][] = $revenue;
      }

      $data['count_products'] = $count_products;
      $data['total_revenue'] = $total_revenue;
      $data['total_profit'] = $total_profit;
      $data['count_orders'] = Order::where('status', '=', 3)
        ->whereYear('created_at', $carbon->year)
        ->whereMonth('created_at', $carbon->month)->count();

      $order_details = OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')->whereYear('created_at', $carbon->year)->whereMonth('created_at', $carbon->month)
        ->whereHas('order', function (Builder $query) {
          $query->where('status', '=', 3);
        })->with([
          'order' => function($query) {
            $query->select('id', 'order_code');
          },
          'product_detail' =>function($query) {
            $query->select('id', 'product_id', 'color', 'import_price')->with([
              'product' => function($query) {
                $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                  'producer' => function($query) {
                    $query->select('id', 'name');
                  }
                ]);
              }
            ]);
          }
        ])->latest()->get();

      $data['order_details'] = $order_details;

      $producers = Producer::select('name')->has('products')->get();

      foreach ($producers as $producer) {
        $data['producer'][$producer->name]['quantity'] = 0;
        $data['producer'][$producer->name]['revenue'] = 0;
        $data['producer'][$producer->name]['profit'] = 0;
      }

      foreach ($order_details as $order_detail) {
        $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] = $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] + $order_detail->quantity;

        $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] = $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] + $order_detail->quantity * $order_detail->price;

        $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] = $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
      }
      $data['text']['title1'] = 'Biểu Đồ Kinh Doanh Tháng '.date('m').' Năm '.date('Y');
      $data['text']['title2'] = 'Danh Sách Sản Phẩm Xuất Kho Tháng '.date('m').' Năm '.date('Y');
      $data['text']['revenue'] = 'DOANH THU THÁNG';
      $data['text']['profit'] = 'LỢI NHUẬN THÁNG';
    } elseif($request->month == null && $request->year != null) {

      $count_products = 0;
      $total_revenue = 0;
      $total_profit = 0;

      for($i = 0; $i < 12; $i++) {

        $data['labels'][] = 'Tháng '.($i + 1);

        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
          ->whereMonth('created_at', $i + 1)
          ->whereYear('created_at', $request->year)
          ->whereHas('order', function (Builder $query) {
            $query->where('status', '=', 3);
          })->with([
            'product_detail' => function($query) {
              $query->select('id', 'import_price');
            }
          ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
          $revenue = $revenue + $order_detail->price * $order_detail->quantity;
          $profit = $profit + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
          $count_products = $count_products + $order_detail->quantity;
        }

        $total_revenue = $total_revenue + $revenue;
        $total_profit = $total_profit + $profit;
        $data['revenues'][] = $revenue;
      }

      $data['count_products'] = $count_products;
      $data['total_revenue'] = $total_revenue;
      $data['total_profit'] = $total_profit;
      $data['count_orders'] = Order::where('status', '=', 3)
        ->whereYear('created_at', $request->year)->count();

      $order_details = OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')->whereYear('created_at', $request->year)
        ->whereHas('order', function (Builder $query) {
          $query->where('status', '=', 3);
        })->with([
          'order' => function($query) {
            $query->select('id', 'order_code');
          },
          'product_detail' =>function($query) {
            $query->select('id', 'product_id', 'color', 'import_price')->with([
              'product' => function($query) {
                $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                  'producer' => function($query) {
                    $query->select('id', 'name');
                  }
                ]);
              }
            ]);
          }
        ])->latest()->get();

      $data['order_details'] = $order_details;

      $producers = Producer::select('name')->has('products')->get();

      foreach ($producers as $producer) {
        $data['producer'][$producer->name]['quantity'] = 0;
        $data['producer'][$producer->name]['revenue'] = 0;
        $data['producer'][$producer->name]['profit'] = 0;
      }

      foreach ($order_details as $order_detail) {
        $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] = $data['producer'][$order_detail->product_detail->product->producer->name]['quantity'] + $order_detail->quantity;

        $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] = $data['producer'][$order_detail->product_detail->product->producer->name]['revenue'] + $order_detail->quantity * $order_detail->price;

        $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] = $data['producer'][$order_detail->product_detail->product->producer->name]['profit'] + $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
      }
      $data['text']['title1'] = 'Biểu Đồ Kinh Doanh Năm '.$request->year;
      $data['text']['title2'] = 'Danh Sách Sản Phẩm Xuất Kho Năm '.$request->year;
      $data['text']['revenue'] = 'DOANH THU NĂM';
      $data['text']['profit'] = 'LỢI NHUẬN NĂM';
    }

    /* Trả về một phản hồi JSON với dữ liệu và trạng thái
    mã 200. */
    return response()->json($data, 200);
  }
}
