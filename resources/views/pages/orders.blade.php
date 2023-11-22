
@extends('layouts.master')

@section('title', 'Đơn Hàng')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Đơn Hàng</li>
      </ol>
    </nav>
  </section>

  <div class="site-orders">
    <section class="section-advertise">
      <div class="content-advertise">
        <div id="slide-advertise" class="owl-carousel">
          @foreach($data['advertises'] as $advertise)
            <div class="slide-advertise-inner" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');" data-dot="<button>{{ $advertise->title }}</button>"></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="section-orders">
      <div class="section-header">
        <h2 class="section-title">Đơn Hàng <span>({{ $data['orders']->count() }} đơn hàng)</span></h2>
      </div>
      <div class="section-content">
        <div class="row">
          <div class="col-md-9">
            <div class="orders-table">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th class="text-center">STT</th>
                      <th class="text-center">Mã<br>Đơn Hàng</th>
                      <th class="text-center">Phương Thức<br>Thanh Toán</th>
                      <th class="text-center">Số Lượng</th>
                      <th class="text-center">Đơn Giá</th>
                      <th class="text-center">Trạng Thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data['orders'] as $key => $order)
                      @php
                        $qty = 0;
                        $price = 0;
                        foreach($order->order_details as $order_detail) {
                          $qty = $qty + $order_detail->quantity;
                          $price = $price + $order_detail->price * $order_detail->quantity;
                        }
                      @endphp
                      <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td class="text-center"><a href="{{ route('order_page', ['id' => $order->id]) }}" title="Chi tiết đơn hàng: {{ $order->order_code }}">{{ $order->order_code }}</a></td>
                        <td class="text-center">{{ $order->payment_method->name }}</td>
                        <td class="text-center">{{ $qty }}</td>
                        <td class="text-center" style="color: #f30;">{{ number_format($price,0,',','.') }}₫</td>
                        @if($order->status)
                        <td class="text-center"><span class="label label-success">Thành Công</span></td>
                        @else
                        <td class="text-center"><span class="label label-danger">Thất Bại</span></td>
                        @endif
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="online_support">
              <h2 class="title">CHÚNG TÔI LUÔN SẴN SÀNG<br>ĐỂ GIÚP ĐỠ BẠN</h2>
              <img src="{{ asset('images/support_online.jpg') }}">
              <h3 class="sub_title">Để được hỗ trợ tốt nhất. Hãy gọi</h3>
              <div class="phone">
                <a href="tel:18006750" title="1800 6750">1800 6750</a>
              </div>
              <div class="or"><span>HOẶC</span></div>
              <h3 class="title">Chat hỗ trợ trực tuyến</h3>
              <h3 class="sub_title">Chúng tôi luôn trực tuyến 24/7.</h3>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

@endsection

@section('css')
  <style>
    .slide-advertise-inner {
      background-repeat: no-repeat;
      background-size: cover;
      padding-top: 21.25%;
    }
    #slide-advertise.owl-carousel .owl-item.active {
      -webkit-animation-name: zoomIn;
      animation-name: zoomIn;
      -webkit-animation-duration: .6s;
      animation-duration: .6s;
    }
  </style>
@endsection

@section('js')
  <script>
    $(document).ready(function(){

      $("#slide-advertise").owlCarousel({
        items: 2,
        autoplay: true,
        loop: true,
        margin: 10,
        autoplayHoverPause: true,
        nav: true,
        dots: false,
        responsive:{
          0:{
            items: 1,
          },
          992:{
            items: 2,
            animateOut: 'zoomInRight',
            animateIn: 'zoomOutLeft',
          }
        },
        navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
      });
    });
  </script>
@endsection
