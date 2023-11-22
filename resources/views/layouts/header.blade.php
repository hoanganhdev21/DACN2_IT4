<header id="header" class="header">
  <div class="container">
    <div class="row display-flex">
      <div class="col-md-2 margin-auto trigger-menu">

        <button type="button" class="navbar-toggle collapsed visible-xs" id="trigger-mobile">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <div class="logo">
          <!-- <a class="logo-wrapper" href="{{ route('home_page') }}" title="{{ config('app.name') }}"><img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"></a> -->
          <a class="logo-wrapper" href="{{ route('home_page') }}" title="{{ config('app.name') }}"><img src="{{ asset('images/logo2.png') }}" alt="Logo"></a>
        </div>
      </div>
      <div class="col-md-3 margin-auto">
        <div class="search">
          <form class="search-bar" action="{{ route('search') }}" method="get" accept-charset="utf-8">
            <input class="input-search" type="search" name="search_key" placeholder="{{ __('Tìm Kiếm') }}" autocomplete="off">
            <button type="submit"><i class="fas fa-search"></i></button>
          </form>
        </div>
      </div>
      <div class="col-md-7 hd-bg-white main-menu-responsive">
        <div class="main-menu">
          <div class="nav">
            <ul>
              <li class="nav-item {{ Helper::check_active(['home_page']) }}"><a href="{{ route('home_page') }}" title="{{ __('Trang Chủ') }}">
                <span class="fas fa-home"></span>
                {{ __('Trang Chủ') }}</a>
              </li>
              <li class="nav-item {{ Helper::check_active(['about_page']) }}"><a href="{{ route('about_page') }}" title="{{ __('Giới Thiệu') }}">
                <span class="fas fa-info"></span>
                {{ __('Giới Thiệu') }}</a>
              </li>
              <li class="nav-item dropdown {{ Helper::check_active(['products_page', 'producer_page', 'product_page']) }}">
                <a href="{{ route('products_page') }}" title="{{ __('Sản Phẩm') }}">
                  <span class="fas fa-mobile-alt"></span>
                  {{ __('Sản Phẩm') }} <i class="fas fa-angle-down"></i>
                </a>
                <div class="dropdown-menu">
                  <ul class="dropdown-menu-item">
                    <li>
                      <h4>{{ __('Danh Mục') }}</h4>
                      <ul class="dropdown-menu-subitem">
                        @foreach($producers as $producer)
                          <li class="{{ Helper::check_param_active('producer_page', $producer->id) }}"><a href="{{ route('producer_page', ['id' => $producer->id]) }}" title="{{ $producer->name }}">{{ $producer->name }}</a></li>
                        @endforeach
                      </ul>
                    </li>
                  </ul>
                </div>
              </li>
              <li class="nav-item {{ Helper::check_active(['posts_page', 'post_page']) }}"><a href="{{ route('posts_page') }}" title="{{ __('Tin Tức') }}">
                <span class="far fa-newspaper"></span>
                {{ __('Tin Tức') }}</a>
              </li>
              <li class="nav-item {{ Helper::check_active(['contact_page']) }}"><a href="{{ route('contact_page') }}" title="{{ __('Liên Hệ') }}">
                <span class="fas fa-id-card"></span>
                {{ __('Liên Hệ') }}</a>
              </li>
            </ul>
          </div>
          <div class="accout-menu">
            <!-- Methood Ktra xem người dùng có được xác thực hay không -->
            @if(Auth::guest())
              <div class="not-logged-menu">
                <ul>
                  <li class="menu-item {{ Helper::check_active(['login']) }}"><a href="{{ route('login') }}" title="{{ __('Đăng Nhập') }}">
                    <span class="fas fa-user"></span>
                    {{ __('Đăng Nhập') }}</a>
                  </li>
                  <li class="menu-item {{ Helper::check_active(['register']) }}"><a href="{{ route('register') }}" title="{{ __('Đăng Ký') }}">
                    <span class="fas fa-key"></span>
                    {{ __('Đăng Ký') }}</a>
                  </li>
                </ul>
              </div>
            @else
              <div class="logged-menu">
                <ul>
                  <li class="menu-item dropdown {{ Helper::check_active(['orders_page', 'order_page', 'show_user', 'edit_user']) }}">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" title="{{ Auth::user()->name }}">
                      <div class="avatar" style="background-image: url('{{ Helper::get_image_avatar_url(Auth::user()->avatar_image) }}');"></div>
                    </a>
                    <ul class="dropdown-menu">
                      @if(Auth::user()->admin)
                      <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Quản Lý Website</a></li>
                      @else
                      <li class="{{ Helper::check_active(['orders_page', 'order_page']) }}"><a href="{{ route('orders_page') }}"><i class="fas fa-clipboard-list"></i> Quản Lý Đơn Hàng</a></li>
                      <li class="{{ Helper::check_active(['show_user', 'edit_user']) }}"><a href="{{ route('show_user') }}"><i class="fas fa-user-cog"></i> Quản Lý Tài Khoản</a></li>
                      @endif
                      <li><a id="logout" action="#"><i class="fas fa-power-off"></i> {{ __('Đăng Xuất') }}</a></li>
                    </ul>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                  </li>
                </ul>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</header><!-- /header -->

<div class="backdrop__body-backdrop___1rvky"></div>
<div class="mobile-main-menu">
  <div class="mobile-main-menu-top">
    <div class="mb-menu-top-header">
      <div class="mb-menu-logo">
        <a class="logo-wrapper" href="{{ route('home_page') }}" title="{{ config('app.name') }}">
          <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
        </a>
      </div>
      <button type="button" id="close-trigger-mobile">
        <i class="fas fa-times"></i>
      </button>
    </div>
    @if(Auth::guest())
    <div class="mb-menu-top-body">
      <div class="mb-menu-user">
        <div style="background-image: url('{{ asset('images/no_login.svg') }}');"></div>
      </div>
      <div class="mb-menu-info">
        <div class="info-top">Đăng Nhập</div>
        <div class="info-bottom">Để nhận nhiều ưu đãi hơn</div>
      </div>
    </div>
    <div class="mb-menu-top-footer">
      <div class="mb-menu-action">
        <a href="{{ route('login') }}" class="btn btn-success">
          <i class="fas fa-user"></i> Đăng Nhập
        </a>
      </div>
      <div class="mb-menu-action">
        <a href="{{ route('register') }}" class="btn btn-warning">
          <i class="fas fa-key"></i> Đăng Ký
        </a>
      </div>
    </div>
    @else
    <div class="mb-menu-top-body">
      <div class="mb-menu-user">
        <div style="background-image: url('{{ Helper::get_image_avatar_url(Auth::user()->avatar_image) }}');"></div>
      </div>
      <div class="mb-menu-info">
        <div class="info-top">{{ Auth::user()->name }}</div>
        <div class="info-bottom">{{ Auth::user()->email }}</div>
      </div>
    </div>
    <div class="mb-menu-top-footer">
      @if(Auth::user()->admin)
      <div class="mb-menu-action">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-success">
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
      </div>
      <div class="mb-menu-action">
        <a id="mobile-logout" href="javascript:void(0);" class="btn btn-danger">
          <i class="fas fa-power-off"></i> {{ __('header.Logout') }}
        </a>
      </div>
      @else
      <div class="mb-menu-action">
        <a href="{{ route('show_user') }}" class="btn btn-success">
          <span class="fas fa-user-cog"></span> Tài Khoản
        </a>
      </div>
      <div class="mb-menu-action">
        <a id="mobile-logout" href="javascript:void(0);" class="btn btn-danger">
          <i class="fas fa-power-off"></i> {{ __('header.Logout') }}
        </a>
      </div>
      @endif
    </div>
    @endif
  </div>
  <div class="mobile-main-menu-middle">
    <div class="mb-menu-middle-header">
      <h3>DANH MỤC</h3>
    </div>
    <div class="mb-menu-middle-body">
      <ul>
        <li class="mb-nav-item {{ Helper::check_active(['home_page']) }}"><a href="{{ route('home_page') }}" title="{{ __('header.Home') }}">
          <span class="fas fa-home"></span>
          {{ __('header.Home') }}</a>
        </li>
        <li class="mb-nav-item {{ Helper::check_active(['about_page']) }}"><a href="{{ route('about_page') }}" title="{{ __('header.About') }}">
          <span class="fas fa-info"></span>
          {{ __('header.About') }}</a>
        </li>
        <li class="mb-nav-item has-item-child {{ Helper::check_active(['products_page', 'producer_page', 'product_page']) }}">
          <a href="{{ route('products_page') }}" title="{{ __('header.Products') }}">
            <span class="fas fa-mobile-alt"></span>
            {{ __('header.Products') }}
          </a>
          <button id="action-collapse" data-toggle="collapse" data-target="#item-child-collapse"><i class="fas fa-plus"></i></button>
          <div id="item-child-collapse" class="collapse">
            <ul>
              @foreach($producers as $producer)
                <li class="{{ Helper::check_param_active('producer_page', $producer->id) }}"><a href="{{ route('producer_page', ['id' => $producer->id]) }}" title="{{ $producer->name }}">{{ $producer->name }}</a></li>
              @endforeach
            </ul>
          </div>
        </li>
        <li class="mb-nav-item {{ Helper::check_active(['posts_page', 'post_page']) }}"><a href="{{ route('posts_page') }}" title="{{ __('header.News') }}">
          <span class="far fa-newspaper"></span>
          {{ __('header.News') }}</a>
        </li>
        <li class="mb-nav-item {{ Helper::check_active(['contact_page']) }}"><a href="{{ route('contact_page') }}" title="{{ __('header.Contact') }}">
          <span class="fas fa-id-card"></span>
          {{ __('header.Contact') }}</a>
        </li>
        @if(Auth::check() && !Auth::user()->admin)
        <li class="mb-nav-item {{ Helper::check_active(['orders_page', 'order_page']) }}"><a href="{{ route('orders_page') }}"><span class="fas fa-clipboard-list"></span> Đơn Hàng</a></li>
        @endif
      </ul>
    </div>
  </div>
  <div class="mobile-main-menu-bottom">
    <div class="mobile-support">
      <div class="text-support">HỖ TRỢ</div>
      <div class="info-support">
        <i class="fa fa-phone" aria-hidden="true"></i> HOTLINE: <a href="tel: +84 967 999 999" title="(+84) 967 999 999">(+84) 967 999 999</a>
      </div>
      <div class="info-support">
        <i class="fa fa-envelope" aria-hidden="true"></i> EMAIL: <a href="mailto:phonestore@gmail.com" title="phonestore@gmail.com">phonestore@gmail.com</a>
      </div>
    </div>
  </div>
</div>
