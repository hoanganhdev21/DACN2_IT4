<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{ Helper::get_image_avatar_url(Auth::user()->avatar_image) }}" class="img-circle" alt="{{ Auth::user()->name }}">
      </div>
      <div class="pull-left info">
        <p>{{ Auth::user()->name }}</p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- search form (Optional) -->
    <form action="#" method="get" class="sidebar-form" id="sidebar-search-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
      </div>
    </form>
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu" data-widget="tree">
      <!-- <li class="header">MAIN NAVIGATION</li> -->
      <!-- Optionally, you can add icons to the links -->
      <li class="{{ Helper::check_active(['admin.dashboard']) }}"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li class="{{ Helper::check_active(['admin.advertise.index', 'admin.advertise.new', 'admin.advertise.edit']) }}"><a href="{{ route('admin.advertise.index') }}"><i class="fa fa-sliders" aria-hidden="true"></i> <span>Quản Lý Quảng Cáo</span></a></li>
      <li class="{{ Helper::check_active(['admin.users', 'admin.user_show']) }}"><a href="{{ route('admin.users') }}"><i class="fa fa-users"></i> <span>Quản Lý Tài Khoản</span></a></li>
      <li class="{{ Helper::check_active(['admin.post.index', 'admin.post.new', 'admin.post.edit']) }}"><a href="{{ route('admin.post.index') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <span>Quản Lý Bài Viết</span></a></li>
      <li class="{{ Helper::check_active(['admin.product.index', 'admin.product.new', 'admin.product.edit']) }}"><a href="{{ route('admin.product.index') }}"><i class="fa fa-product-hunt" aria-hidden="true"></i> <span>Quản Lý Sản Phẩm</span></a></li>
      <li class="{{ Helper::check_active(['admin.order.index', 'admin.order.show']) }}"><a href="{{ route('admin.order.index') }}"><i class="fa fa-list-alt" aria-hidden="true"></i> <span>Quản Lý Đơn Hàng</span></a></li>
      <li class="{{ Helper::check_active(['admin.warehouse.index']) }}"><a href="{{route('admin.warehouse')}}"><i class="fa fa-archive" aria-hidden="true"></i><span>Kho Hàng</span></a></li>
      <li class="{{ Helper::check_active(['admin.warehouse.orderDetail']) }}"><a href="{{route('admin.orderDetails')}}"><i class="fa fa-archive" aria-hidden="true"></i><span>Thống Kê Đơn Hàng</span></a></li>
      <li class="{{ Helper::check_active(['admin.statistic']) }}"><a href="{{ route('admin.statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i> <span>Thống Kê Doanh Thu</span></a></li>
    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>
