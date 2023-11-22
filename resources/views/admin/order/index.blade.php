@extends('admin.layouts.master')

@section('title', 'Quản Lý Đơn Hàng')

@section('embed-css')
<link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('custom-css')
<style>
  #order-table td,
  #order-table th {
    vertical-align: middle !important;
  }
  #order-table span.status-label {
    display: block;
    width: 85px;
    text-align: center;
    padding: 2px 0px;
  }
  #search-input span.input-group-addon {
    padding: 0;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 34px;
    border: none;
    background: none;
  }
  #search-input span.input-group-addon i {
    font-size: 18px;
    line-height: 34px;
    width: 34px;
    color: #f30;
  }
  #search-input input {
    position: static;
    width: 100%;
    font-size: 15px;
    line-height: 22px;
    padding: 5px 5px 5px 34px;
    float: none;
    height: unset;
    border-color: #fbfbfb;
    box-shadow: none;
    background-color: #e8f0fe;
    border-radius: 5px;
  }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li class="active">Quản Lý Đơn Hàng</li>
</ol>
@endsection

@section('content')

  <!-- Main row -->
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <div class="row">
            <div class="col-md-5 col-sm-6 col-xs-6">
              <div id="search-input" class="input-group">
                <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                <input type="text" class="form-control" placeholder="search...">
              </div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-6">
              <div class="btn-group pull-right">
                <a href="{{ route('admin.order.index') }}" class="btn btn-flat btn-primary" title="Refresh">
                  <i class="fa fa-refresh"></i><span class="hidden-xs"> Refresh</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <table id="order-table" class="table table-hover" style="width:100%; min-width: 1024px;">
            <thead>
              <tr>
                <th data-width="10px">ID</th>
                <th data-orderable="false" data-width="85px">Mã Đơn Hàng</th>
                <th data-orderable="false" data-width="100px">Tài Khoản</th>
                <th data-orderable="false" data-width="100px">Tên</th>
                <th data-orderable="false">Email</th>
                <th data-orderable="false" data-width="70px">Điện Thoại</th>
                <th data-orderable="false">Phương Thức Thanh Toán</th>
                <th data-width="60px" data-type="date-euro">Ngày Tạo</th>
                <th data-width="66px">Trạng Thái</th>
                <th data-orderable="false" data-width="130px">Tác Vụ</th>
              </tr>
            </thead>
            
            <tbody>
              @foreach($orders as $order)
                <tr>
                  <td class="text-center">{{ $order->id }}</td>
                  <td>{{ '#'.$order->order_code }}</td>
                  <td>
                    <a href="{{ route('admin.user_show', ['id' => $order->user->id]) }}" class="text-left" title="{{ $order->user->name }}">{{ $order->user->name }}</a>
                  </td>
                  <td>{{ $order->name }}</td>
                  <td>{{ $order->email }}</td>
                  <td>{{ $order->phone }}</td>
                  <td>{{ $order->payment_method->name }}</td>
                  <td> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y')}}</td>
                  <td>
                
                   @if($order->status == 1)
                   <span class="label label-default" style="font-size:13px">Đang Xử Lý</span>
                   @elseif ($order->status == 2)
                   <span class="label label-info" style="font-size:13px">Đang Vận Chuyển</span>
                   @elseif ($order->status == 3)
                   <span class="label label-success" style="font-size:13px">Đã Giao Hàng</span>
                   @else
                   <span class="label label-danger" style="font-size:13px">Hủy</span>
                   @endif
                  </td>
                  <td>
                  <div class="btn-group">
                  <button type="button" class="btn btn-success btn-xs" style=" height: 30px;">Action  </button>
                  <button type="button" style="height: 30px;" class="btn btn-success btn-xs dropdown-toggle"
                  data-toggle="dropdown" aria-expanded='false'>
                    <span class="caret"></span>
                    <span class="sr-only">Toggle-dropdown</span>
                    </button>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="{{route('admin.orderTransaction',['process',$order->id])}}"><i class="fa fa-ban"> </i>Đang Vận Chuyển</a>
                    </li>
                    <li>
                      <a href="{{route('admin.orderTransaction',['success',$order->id])}}" ><i class="fa fa-ban"> </i>Đã Giao Hàng</a>
                    </li>
                    <li>
                      <a href="{{route('admin.orderTransaction',['cancel',$order->id])}}" ><i class="fa fa-ban"> </i>Hủy</a>
                    </li>
                  </ul>
                    <a href="{{ route('admin.order.show', ['id' => $order->id]) }}" class="btn btn-icon btn-sm btn-primary tip" title="Chi Tiết">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
@endsection

@section('embed-js')
  <!-- DataTables -->
  <script src="{{ asset('AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
  <!-- SlimScroll -->
  <script src="{{ asset('AdminLTE/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
  <!-- FastClick -->
  <script src="{{ asset('AdminLTE/bower_components/fastclick/lib/fastclick.js') }}"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-euro.js"></script>
@endsection

@section('custom-js')
<script>
  $(function () {
    var table = $('#order-table').DataTable({
      "language": {
        "zeroRecords":    "Không tìm thấy kết quả phù hợp",
        "info":           "Hiển thị trang <b>_PAGE_/_PAGES_</b> của <b>_TOTAL_</b> đơn hàng",
        "infoEmpty":      "Hiển thị trang <b>1/1</b> của <b>0</b> đơn hàng",
        "infoFiltered":   "(Tìm kiếm từ <b>_MAX_</b> đơn hàng)",
        "emptyTable": "Không có dữ liệu đơn hàng",
      },
      "lengthChange": false,
       "autoWidth": false,
       "order": [],
      "dom": '<"table-responsive"t><<"row"<"col-md-6 col-sm-6"i><"col-md-6 col-sm-6"p>>>',
      "drawCallback": function(settings) {
        var api = this.api();
        if (api.page.info().pages <= 1) {
          $('#'+ $(this).attr('id') + '_paginate').hide();
        }
      }
    });

    $('#search-input input').on('keyup', function() {
        table.search(this.value).draw();
    });
  });
</script>
@endsection
