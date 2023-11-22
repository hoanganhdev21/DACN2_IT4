
@extends('layouts.master')

@section('title', $data['user']->name)

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tài Khoản</li>
      </ol>
    </nav>
  </section>

  <div class="site-user">
    <section class="section-advertise">
      <div class="content-advertise">
        <div id="slide-advertise" class="owl-carousel">
          @foreach($data['advertises'] as $advertise)
            <div class="slide-advertise-inner" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');" data-dot="<button>{{ $advertise->title }}</button>"></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="section-user">
      <div class="section-header">
        <h2 class="section-title">Thông Tin Tài Khoản</h2>
      </div>
      <div class="section-content">
        <div class="row">
          <div class="col-lg-9 col-md-8">
            <div class="user">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                  <div class="user-avatar">
                    <img src="{{ Helper::get_image_avatar_url($data['user']->avatar_image) }}" title="{{ $data['user']->name }}">
                  </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-8 col-xs-8">
                  <div class="user-info">
                    <div class="info">
                      <div class="info-label">Tên Tài Khoản</div>
                      <div class="info-content">{{ $data['user']->name }}</div>
                    </div>
                    <div class="info">
                      <div class="info-label">Email</div>
                      <div class="info-content">{{ $data['user']->email }}</div>
                    </div>
                    <div class="info">
                      <div class="info-label">Số Điện Thoại</div>
                      <div class="info-content">{{ $data['user']->phone }}</div>
                    </div>
                    <div class="info">
                      <div class="info-label">Địa Chỉ</div>
                      <div class="info-content">{{ $data['user']->address }}</div>
                    </div>
                  </div>
                  <div class="action-edit">
                    <a href="{{ route('edit_user') }}" class="btn btn-default" title="Thay đổi thông tin cá nhân">Thay Đổi Thông Tin</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-4">
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

      @if(session('alert'))
        Swal.fire(
          '{{ session('alert')['title'] }}',
          '{{ session('alert')['content'] }}',
          '{{ session('alert')['type'] }}'
        )
      @endif
    });
  </script>
@endsection
