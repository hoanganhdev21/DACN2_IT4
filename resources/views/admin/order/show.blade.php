@extends('admin.layouts.master')

@section('title', 'Đơn Hàng: #'.$order->order_code)

@section('embed-css')
@endsection

@section('custom-css')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li><a href="{{ route('admin.order.index') }}"><i class="fa fa-list-alt" aria-hidden="true"></i> Quản Lý Đơn Hàng</a></li>
  <li class="active">{{ 'Đơn Hàng: #'.$order->order_code }}</li>
</ol>
@endsection

@section('content')
  <!-- Main content -->
  <section class="invoice" style="margin: 0;">
    <div id="print-invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <div style="display: inline-flex; align-items: center;">
              <div style="width: 30px; margin-right: 5px;">
                <img src="{{ asset('images/favicon.png') }}" alt="PhoneStore Logo" style="width: 100%; height: auto; object-fit: cover;">
              </div>
              <div style="color: #f33;">VDO</div>
            </div>
            <small class="pull-right">Date: {{ date("d/m/Y") }}</small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="invoice-info">
        <div class="row">
          <div class="col-md-3 col-sm-3 col-xs-3">
            From: <br>
            <br>
            <address>
              <b>Admin VDO</b><br>
              Phone: (+84) 343 754 517<br>
              Email: thanhtrung@gmail.com<br>
              Address: Đinh Bộ Lĩnh - P 25 - Bình Thạnh.
            </address>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3">
            To: <br>
            <br>
            <address>
              <b>{{ $order->name }}</b><br>
              Phone: {{ $order->phone }}<br>
              Email: {{ $order->email }}<br>
              Address: {{ $order->address }}
            </address>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3">
            Thông Tin Tài Khoản<br>
            <br>
            <address>
              <b>{{ $order->user->name }}</b><br>
              Phone: {{ $order->user->phone }}<br>
              Email: {{ $order->user->email }}<br>
              Address: {{ $order->user->address }}
            </address>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3">
            Thông Tin Đơn Hàng<br>
            <br>
            <address>
              <b>Đơn Hàng #{{ $order->order_code }}</b><br>
              <b>Ngày Tạo:</b> {{ date_format($order->created_at, 'd/m/Y') }}<br>
              <b>Thanh Toán:</b> {{ $order->payment_method->name }}
            </address>
          </div>
        </div>
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th style="text-align: center;">STT</th>
                <th>Mã Sản Phẩm</th>
                <th>Tên Sản Phẩm</th>
                <th>Mầu Sắc</th>
                <th style="text-align: center;">Số Lượng</th>
                <th>Đơn Giá</th>
                <th>Tổng Tiền</th>
              </tr>
            </thead>
            <tbody>
              <?php $price = 0; ?>
              @foreach($order->order_details as $key => $order_detail)
                <?php $price = $price + $order_detail->price * $order_detail->quantity; ?>
                <tr>
                  <td style="text-align: center;">{{ $key + 1 }}</td>
                  <td>{{ '#'.$order_detail->product_detail->product->sku_code }}</td>
                  <td>{{ $order_detail->product_detail->product->name }}</td>
                  <td>{{ $order_detail->product_detail->color }}</td>
                  <td style="text-align: center;">{{ $order_detail->quantity }}</td>
                  <td><span style="color: #f30;">{{ number_format($order_detail->price,0,',','.') }} VNĐ</span></td>
                  <td><span style="color: #f30;">{{ number_format($order_detail->price * $order_detail->quantity,0,',','.') }} VNĐ</span></td>
                </tr>
              @endforeach
              <tr>
                  <td colspan="7" style="text-align: right;"><b>Tổng = <span style="color: #f30;">{{ number_format($price,0,',','.') }} VNĐ</span></b></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
          <p class="lead">Phương Thức Thanh Toán:</p>
          @if(Str::contains($order->payment_method->name, 'Online Payment'))
            <div style="width: fit-content; padding: 5px; border: 1px solid #e3e3e3; border-radius: 5px; box-shadow: 0px 0px 5px #e3e3e3;">
              <img style="width: 150px; height: 50px; object-fit: cover;" src="{{ asset('images/nganluong.png') }}" alt="Ngân Lượng">
            </div>
          @else
            <div style="width: fit-content; padding: 5px; border: 1px solid #e3e3e3; border-radius: 5px; box-shadow: 0px 0px 5px #e3e3e3;">
              <img style="width: 150px; height: 60px; object-fit: cover;" src="{{ asset('images/cod.png') }}" alt="COD">
            </div>
          @endif

          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <b>{{ $order->payment_method->name.':' }}</b><br>
            <span style="margin-left: 15px;">{{ $order->payment_method->describe }}</span>
          </p>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <p class="lead">Thanh Toán Chi Tiết</p>

          <div class="table-responsive">
            <table class="table">
              <tr>
                <th style="width:50%">Tổng Tiền:</th>
                <td><span style="color: #f30;">{{ number_format($price,0,',','.') }} VNĐ</span></td>
              </tr>
              <tr>
                <th>Đã Thanh Toán:</th>
                @if(Str::contains($order->payment_method->name, 'Online Payment'))
                <td><span style="color: #f30;">{{ number_format($price,0,',','.') }} VNĐ</span></td>
                @else
                <td><span style="color: #f30;">0 VNĐ</span></td>
                @endif
              </tr>
              <tr>
                <th>Phí Vận Chuyển:</th>
                <td><span style="color: #f30;">0 VNĐ</span></td>
              </tr>
              <tr>
                <th>Tổng Số Tiền Phải Thanh Toán:</th>
                @if(Str::contains($order->payment_method->name, 'Online Payment'))
                <td><span style="color: #f30;">0 VNĐ</span></td>
                @else
                <td><span style="color: #f30;">{{ number_format($price,0,',','.') }} VNĐ</span></td>
                @endif
              </tr>
            </table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- this row will not appear when printing -->
    <div class="row no-print">
      <div class="col-xs-12">
        <button class="btn btn-success btn-print pull-right"><i class="fa fa-print"></i> In Hóa Đơn</button>
      </div>
    </div>
  </section>
  <!-- /.content -->
  <div class="clearfix"></div>
@endsection

@section('embed-js')
<!-- Print JS -->
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
@endsection

@section('custom-js')
<script>
  $(document).ready(function() {
    $('.btn-print').click(function(){
      printJS({
        printable: 'print-invoice',
        type: 'html',
        css: [
          '{{ asset('AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css') }}',
          '{{ asset('AdminLTE/dist/css/AdminLTE.min.css') }}'
        ],
        style: 'img { filter: grayscale(100%); -webkit-filter: grayscale(100%); }'
      });
    });
  });
</script>
@endsection
