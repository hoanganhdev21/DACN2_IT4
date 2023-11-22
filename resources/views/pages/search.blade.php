
@extends('layouts.master')

@section('title', 'Tìm Kiếm')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tìm Kiếm: {{ Request::get('search_key') }}</li>
      </ol>
    </nav>
  </section>

  <div class="site-search">
    <section class="section-advertise">
      <div class="content-advertise">
        <div id="slide-advertise" class="owl-carousel">
          @foreach($data['advertises'] as $advertise)
            <div class="slide-advertise-inner" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');" data-dot="<button>{{ $advertise->title }}</button>"></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="section-products">
      <div class="section-header">
        <h2 class="section-title">Sản Phẩm <span>( {{ $data['products']->count() }} sản phẩm )</span></h2>
      </div>
      <div class="section-content">
        @if($data['products']->isEmpty())
          <div class="empty-content">
            <div class="icon"><i class="fab fa-searchengin"></i></div>
            <div class="title">Oooops!</div>
            <div class="content">Product Item Not Found</div>
          </div>
        @else
          <div class="row">
            @foreach($data['products'] as $key => $product)
              <div class="col-md-2 col-md-20">
                <div class="item-product">
                  <a href="{{ route('product_page', ['id' => $product->id]) }}" title="{{ $product->name }}">
                    <div class="row">
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="image-product" style="background-image: url('{{ Helper::get_image_product_url($product->image) }}');padding-top: 100%;">
                          {!! Helper::get_promotion_percent($product->product_detail->sale_price, $product->product_detail->promotion_price, $product->product_detail->promotion_start_date, $product->product_detail->promotion_end_date) !!}
                        </div>
                        <div class="content-product">
                          <h3 class="title">{{ $product->name }}</h3>
                          <div class="start-vote">
                            {!! Helper::get_start_vote($product->rate) !!}
                          </div>
                          <div class="price">
                            {!! Helper::get_real_price($product->product_detail->sale_price, $product->product_detail->promotion_price, $product->product_detail->promotion_start_date, $product->product_detail->promotion_end_date) !!}
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12 col-sm-12 col-xs-12 animate">
                        <div class="product-details">
                          <p><strong><i class="fas fa-tv"></i> Màn Hình: </strong>{{ $product->monitor }}</p>
                          <p><strong><i class="fas fa-camera-retro"></i> Camera Trước: </strong>{{ $product->front_camera }}</p>
                          <p><strong><i class="fas fa-camera-retro"></i> Camera sau: </strong>{{ $product->rear_camera }}</p>
                          <p><strong><i class="fas fa-microchip"></i> CPU: </strong>{{ $product->CPU }}</p>
                          <p><strong><i class="fas fa-microchip"></i>GPU: </strong>{{ $product->GPU }}</p>
                          <p><strong><i class="fas fa-hdd"></i> RAM: </strong>{{ $product->RAM }}GB</p>
                          <p><strong><i class="fas fa-hdd"></i> Bộ Nhớ Trong: </strong>{{ $product->ROM }}GB</p>
                          @if(Str::is('*Android*', $product->OS_version))
                            <p><strong><i class="fab fa-android"></i> HĐH: </strong>{{ $product->OS_version }}</p>
                          @elseif(Str::is('*IOS*', $product->OS_version))
                            <p><strong><i class="fab fa-apple"></i> HĐH: </strong>{{ $product->OS_version }}</p>
                          @elseif(Str::is('*Windows*', $product->OS_version))
                            <p><strong><i class="fab fa-windows"></i> HĐH: </strong>{{ $product->OS_version }}</p>
                          @endif
                          <p><strong><i class="fas fa-battery-full"></i> Dung Lượng PIN: </strong>{{ $product->pin }}</p>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </section>

    <section class="section-posts">
      <div class="section-header">
        <h2 class="section-title">Bài viết <span>( {{ $data['posts']->count() }} bài viết )</span></h2>
      </div>
      <div class="section-content">
        @if($data['posts']->isEmpty())
          <div class="empty-content">
            <div class="icon"><i class="fab fa-searchengin"></i></div>
            <div class="title">Oooops!</div>
            <div class="content">Posts Item Not Found</div>
          </div>
        @else
          <div class="row">
            @foreach($data['posts'] as $post)
              <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="item-post">
                  <a href="{{ route('post_page', ['id' => $post->id]) }}" title="{{ $post->title }}">
                    <div class="image-post" style="background-image: url('{{ Helper::get_image_post_url($post->image) }}');padding-top: 50%;">
                    </div>
                    <div class="title-post">
                      <h3>{{ $post->title }}</h3>
                    </div>
                    <div class="date-post">
                      Ngày đăng: {{ date_format($post->created_at, 'd/m/Y') }}
                    </div>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
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
