@extends('layouts.master')

@section('title', 'Giỏ Hàng')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Giỏ Hàng</li>
      </ol>
    </nav>
  </section>

  <div class="site-cart">
    <section class="section-advertise">
      <div class="content-advertise">
        <div id="slide-advertise" class="owl-carousel">
          @foreach($advertises as $advertise)
            <div class="slide-advertise-inner" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');" data-dot="<button>{{ $advertise->title }}</button>"></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="section-cart">
      <div class="section-header">
        <h2 class="section-title">Giỏ Hàng <span>( {{ $cart->totalQty }} Sản Phẩm )</span></h2>
      </div>
      <div class="section-content">
        @if(!$cart->totalQty)
          <div class="row">
            <div class="col-md-4 col-md-offset-4">
              <div class="cart-empty">
                <img src="{{ asset('images/empty-cart.png') }}" alt="Giỏ Hàng Trống">
                <div class="btn-cart-empty">
                  <a href="{{ route('products_page') }}" title="Tiếp tục mua sắm">Tiếp Tục Mua Sắm</a>
                </div>
              </div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="col-md-8">
              <div class="cart-items">
                @foreach($cart->items as $key => $item)
                  <div class="item product-{{ $key }}">
                    <div class="image-product">
                      <a href="{{ route('product_page', ['id' => $item['item']->product->id]) }}" target="_blank" title="{{ $item['item']->product->name . ' - ' . $item['item']->color }}">
                        <img src="{{ Helper::get_image_product_url($item['item']->product->image) }}">
                      </a>
                    </div>
                    <div class="info-product">
                      <div class="name"><a href="{{ route('product_page', ['id' => $item['item']->product->id]) }}" target="_blank" title="{{ $item['item']->product->name . ' - ' . $item['item']->color }}">{{ $item['item']->product->name . ' - ' . $item['item']->color }}</a></div>
                      <div class="price">{!! Helper::get_real_price($item['item']->sale_price, $item['item']->promotion_price, $item['item']->promotion_start_date, $item['item']->promotion_end_date) !!}</div>
                      <div class="quantity-block">
                        <div class="input-group-btn">
                          <button onclick="minus({{ $key }});" class="reduced_pop items-count btn-minus btn btn-default bootstrap-touchspin-down" type="button">–</button>
                          <input type="text" onchange="if(this.value == 0) this.value=1;" maxlength="12" min="1" max="{{ $item['item']->quantity }}" disabled class="form-control quantity-r2 quantity js-quantity-product input-text number-sidebar input_pop input_pop qtyItem{{ $key }}" id="qtyItem{{ $key }}" name="Lines" size="4" value="{{$item['qty']}}" data-url="{{ route('update_cart') }}">
                          <button onclick="plus({{ $key }});" class="increase_pop items-count btn-plus btn btn-default bootstrap-touchspin-up" type="button">+</button>
                        </div>
                      </div>
                      <div class="remove-item">
                        <a href="javascript:;" onclick="removeItem($(this));" class="btn btn-link btn-item-delete remove-item-cart" data-id="{{ $key }}" title="Xóa" data-id="{{ $key }}" data-url="{{ route('remove_cart') }}"><i class="fas fa-times"></i></a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="col-md-4">
              <div class="total-price">
                <div class="box-price-top">
                  <div class="title">Tạm Tính</div>
                  <div class="price">{{ number_format($cart->totalPrice,0,',','.') }}₫</div>
                </div>
                <div class="box-price-up">
                  <div class="title">Thành Tiền</div>
                  <div class="price">{{ number_format($cart->totalPrice,0,',','.') }}₫</div>
                </div>
                <div class="btn-action">
                  <button title="Thanh Toán Ngay" data-url="{{ route('show_checkout') }}">Thanh Toán Ngay</button>
                  <a href="{{ route('products_page') }}" class="btn-btn-default" title="Tiếp Tục Mua Hàng">Tiếp Tục Mua Hàng</a>
                </div>
              </div>
            </div>
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
  <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
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
  <script src="{{ asset('js/cart.js') }}"></script>
@endsection
