
@extends('layouts.master')

@section('title', 'Tin Tức')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tin Tức</li>
      </ol>
    </nav>
  </section>

  <div class="site-posts">
    <section class="section-advertise">
      <div class="content-advertise">
        <div id="slide-advertise" class="owl-carousel">
          @foreach($data['advertises'] as $advertise)
            <div class="slide-advertise-inner" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');" data-dot="<button>{{ $advertise->title }}</button>"></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="section-posts">
      <div class="row">
        <div class="col-lg-8 col-md-8">
          <div class="section-left">
            <div class="section-header">
              <h2 class="section-title">Tin Tức</h2>
            </div>
            <div class="section-content">
              @if($data['posts']->isEmpty())
                <div class="empty-content">
                  <div class="icon"><i class="fab fa-searchengin"></i></div>
                  <div class="title">Oooops!</div>
                  <div class="content">Posts Item Not Found</div>
                </div>
              @else
                @foreach($data['posts'] as $post)
                  <a href="{{ route('post_page', ['id' => $post->id]) }}" title="{{ $post->title }}">
                    <div class="item-post">
                      <div class="image-post" style="background-image: url('{{ Helper::get_image_post_url($post->image) }}');">
                      </div>
                      <div class="info-post">
                        <div class="title-post">
                          <h3>{{ $post->title }}</h3>
                        </div>
                        <div class="desc-post">
                          <span><i class="fas fa-user"></i> Admin</span>
                          <span><i class="fas fa-clock"></i> {{ date_format($post->created_at, 'd/m/Y') }}</span>
                        </div>
                      </div>
                    </div>
                  </a>
                @endforeach
              @endif
            </div>
            @if($data['posts']->isNotEmpty())
              <div class="section-footer text-center">
                {{ $data['posts']->links() }}
              </div>
            @endif
          </div>
        </div>
        <div class="col-lg-4 col-md-4">
          <div class="section-right">
            <div class="suggest-product">
              <div class="suggest-header">
                <h2>Sản Phẩm Mới Nhất</h2>
              </div>
              @if($data['suggest_products']->isNotEmpty())
                <div class="suggest-content">
                  @foreach($data['suggest_products'] as $product)
                    <a href="{{ route('product_page', ['id' => $product->id]) }}" title="{{ $product->name }}">
                      <div class="product-content">
                        <div class="image">
                          <img src="{{ Helper::get_image_product_url($product->image) }}">
                        </div>
                        <div class="content">
                          <h3 class="title">{{ $product->name }}</h3>
                          <div class="start-vote">
                            {!! Helper::get_start_vote($product->rate) !!}
                          </div>
                          <div class="price">
                            {!! Helper::get_real_price($product->product_detail->sale_price, $product->product_detail->promotion_price, $product->product_detail->promotion_start_date, $product->product_detail->promotion_end_date) !!}
                          </div>
                        </div>
                      </div>
                    </a>
                  @endforeach
                </div>
              @endif
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
