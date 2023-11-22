@extends('admin.layouts.master')

@section('title', 'User Profile')

@section('embed-css')
@endsection

@section('custom-css')
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li><a href="{{ route('admin.users') }}"><i class="fa fa-users"></i> Quản Lý Tài Khoản</a></li>
  <li class="active">{{ $user->name }}</li>
</ol>
@endsection

@section('content')
<div class="row">
  <div class="col-md-3">

    <!-- Profile Image -->
    <div class="box box-primary">
      <div class="box-body box-profile">
        <img class="profile-user-img img-responsive img-circle" src="{{ Helper::get_image_avatar_url($user->avatar_image) }}" alt="User profile picture">

        <h3 class="profile-username text-center">{{ $user->name }}</h3>

        @if($user->active)
        <p class="text-center"><span class="label label-success">Activated</span></p>
        @else
        <p class="text-center"><span class="label label-danger">Not Activated</span></p>
        @endif

        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>Email</b> <a class="pull-right">{{ $user->email }}</a>
          </li>
          <li class="list-group-item">
            <b>Số điện thoại</b> <a class="pull-right">{{ $user->phone }}</a>
          </li>
          <li class="list-group-item">
            <b>Liên kết tài khoản</b> <a class="pull-right">{{ $user->provider ?: 'Không' }}</a>
          </li>
          <li class="list-group-item">
            <b>Ngày tạo</b> <a class="pull-right">{{ date_format($user->created_at, 'd/m/Y') }}</a>
          </li>
        </ul>
        <strong><i class="fa fa-map-marker margin-r-5"></i> Địa chỉ</strong>
        <p class="text-muted">{{ $user->address }}</p>
        @if(!$user->active)
        <a href="{{ route('admin.user_send', ['id' => $user->id]) }}" class="btn btn-warning btn-block"><b>Send Active Account</b></a>
        @endif
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
  <div class="col-md-9">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#comment-timeline" data-toggle="tab">Lịch Sử Bình Luận</a></li>
        <li><a href="#order-timeline" data-toggle="tab">Lịch Sử Mua Hàng</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="comment-timeline">
          @if($product_votes->isNotEmpty())
          <!-- The timeline -->
          <ul class="timeline timeline-inverse">
            @foreach($product_votes as $vote)
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-red">
                  <i class="fa fa-clock-o"></i> {{ date_format($vote->created_at, 'H:i d/m/Y') }}
                </span>
              </li>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <li>
                <i class="fa fa-comments bg-blue" aria-hidden="true"></i>

                <div class="timeline-item">

                  <h3 class="timeline-header"><a>{{ $user->name }}</a> đã đánh giá <a>{{ $vote->rate }}</a> sao về sản phẩm <a>{{ $vote->product->name }}</a></h3>

                  <div class="timeline-body">
                    <b>Nội Dung:</b> {{ $vote->content }}
                  </div>
                  <div class="timeline-footer">
                    <span class="label label-warning">{{ $vote->rate }} Sao</span>
                  </div>
                </div>
              </li>
              <!-- END timeline item -->
            @endforeach
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
          @else
          <div style="width: 100%; padding-top: 30%; position: relative;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20px;">Lịch Sử Bình Luận Trống!</div>
          </div>
          @endif
        </div>
        <!-- /.tab-pane -->

        <div class="tab-pane" id="order-timeline">
          @if($orders->isNotEmpty())
          <!-- The timeline -->
          <ul class="timeline timeline-inverse">
            @foreach($orders as $order)

              @php
                $qty = 0;
                $price = 0;
                foreach($order->order_details as $order_detail) {
                  $qty = $qty + $order_detail->quantity;
                  $price = $price + $order_detail->price * $order_detail->quantity;
                }
              @endphp
              <!-- timeline time label -->
              <li class="time-label">
                <span class="bg-yellow">
                  <i class="fa fa-clock-o"></i> {{ date_format($order->created_at, 'H:i d/m/Y') }}
                </span>
              </li>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <li>
                @if($order->status)
                <i class="fa fa-check bg-green" aria-hidden="true"></i>
                @else
                <i class="fa fa-times bg-red" aria-hidden="true"></i>
                @endif

                <div class="timeline-item">

                  <h3 class="timeline-header"><a>{{ $user->name }}</a> đã mua <b style="color: #f30;">{{ $qty }}</b> sản phẩm với giá trị <b style="color: #f30;">{{ number_format($price,0,',','.') }}</b> VNĐ</h3>

                  <div class="timeline-body">
                    <div class="table-responsive">
                      <table class="table table-striped" style="margin-bottom: 0; background-color: #fff;">
                        <thead>
                          <tr>
                            <th class="text-center" style="vertical-align: middle;">STT</th>
                            <th class="text-center" style="vertical-align: middle;">Mã<br>Sản Phẩm</th>
                            <th class="text-center" style="vertical-align: middle;">Tên<br>Sản Phẩm</th>
                            <th class="text-center" style="vertical-align: middle;">Mầu Sắc</th>
                            <th class="text-center" style="vertical-align: middle;">Số Lượng</th>
                            <th class="text-center" style="vertical-align: middle;">Đơn Giá</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($order->order_details as $key => $order_detail)
                            <tr>
                              <td class="text-center" style="vertical-align: middle;">{{ $key + 1 }}</td>
                              <td class="text-center" style="vertical-align: middle;"><a title="{{ $order_detail->product_detail->product->name }}">{{ $order_detail->product_detail->product->sku_code }}</a></td>
                              <td class="text-center" style="vertical-align: middle;">{{ $order_detail->product_detail->product->name }}</td>
                              <td class="text-center" style="vertical-align: middle;">{{ $order_detail->product_detail->color }}</td>
                              <td class="text-center" style="vertical-align: middle;">{{ $order_detail->quantity }}</td>
                              <td class="text-center" style="color: #f30; vertical-align: middle;">{{ number_format($order_detail->price,0,',','.') }}₫</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="timeline-footer">
                    @if($order->status)
                    <span class="label label-success">Thành Công</span>
                    @else
                    <span class="label label-danger">Thất Bại</span>
                    @endif
                  </div>
                </div>
              </li>
              <!-- END timeline item -->
            @endforeach
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
          @else
          <div style="width: 100%; padding-top: 30%; position: relative;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20px;">Lịch Sử Mua Hàng Trống!</div>
          </div>
          @endif
        </div>
        <!-- /.tab-pane -->
      </div>
      <!-- /.tab-content -->
    </div>
    <!-- /.nav-tabs-custom -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
@endsection

@section('embed-js')

@endsection

@section('custom-js')
<script>

</script>
@endsection
