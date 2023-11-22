@extends('admin.layouts.master')

@section('title', 'Kho Hàng')

@section('embed-css')
<link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('custom-css')
<style>
  #product-table td,
  #product-table th {
    vertical-align: middle !important;
  }
  #product-table span.status-label {
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
  <li class="active">Kho Hàng</li>
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
                <a href="{{ route('admin.warehouse') }}" class="btn btn-flat btn-primary" title="Refresh" style="margin-right: 5px;">
                  <i class="fa fa-refresh"></i><span class="hidden-xs"> Refresh</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <table id="product-table" class="table table-hover" style="width:100%; min-width: 985px;">
            <thead>
              <tr>
                <th data-width="10px">STT</th>
                <th data-orderable="false" data-width="75px">Hình Ảnh</th>
                <th data-orderable="false" data-width="85px">Mã Sản Phẩm</th>
                <th data-orderable="false">Tên Sản Phẩm</th>
                <th data-width="90px">Hệ điều hành</th>
                <th data-width="66px">Màu sắc</th>
                <th data-width="100px">Số Lượng Nhập</th>
                <th data-width="66px">Đã Bán</th>
                <th data-width="66px">Còn Lại</th>
                <th data-width="70px" data-type="date-euro">Ngày Nhập</th>
              </tr>
            </thead>
            <tbody>
            @foreach($product_details as $product_dt)
                <tr>
                  <td class="text-center">{{ $product_dt->id }}</td>
                  <td>
                  <div style="background-image: url('{{ Helper::get_image_product_url($product_dt->image) }}'); padding-top: 100%; background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                  </td>
                  <td>{{ $product_dt->sku_code }}</td>
                  <td>{{ $product_dt->name }}</td>
                  <td>{{ $product_dt->OS }}</td>
                  <td>{{$product_dt->color}}</td>
                  <td>{{$product_dt->quantity}}</td>
                  <td>{{$product_dt->orderDetailQuantity ? $product_dt->orderDetailQuantity : 0}}</td>
                  <td>{{$product_dt->conlai ? $product_dt->conlai : $product_dt->quantity}}</td>
                  <td>{{ \Carbon\Carbon::parse($product_dt->created_at)->format('d/m/Y')}}</td>
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
    var table = $('#product-table').DataTable({
      "language": {
        "zeroRecords":    "Không tìm thấy kết quả phù hợp",
        "info":           "Hiển thị trang <b>_PAGE_/_PAGES_</b> của <b>_TOTAL_</b> sản phẩm",
        "infoEmpty":      "Hiển thị trang <b>1/1</b> của <b>0</b> sản phẩm",
        "infoFiltered":   "(Tìm kiếm từ <b>_MAX_</b> sản phẩm)",
        "emptyTable": "Không có dữ liệu sản phẩm",
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
