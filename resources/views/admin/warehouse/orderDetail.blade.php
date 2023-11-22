@extends('admin.layouts.master')

@section('title', 'Thống Kê Đơn Hàng')

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
  <li class="active">Chi Tiết Đơn Hàng</li>
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
            <div class="col-md-3 col-sm-6 col-xs-6" style="float:right">
              <div class="input-groups">
                 
                <form action="{{route('admin.orderDetails')}}" method="GET">
                <div><input type="date" class="form-control pull-right" id="reservation" name="date_to" autocomplete="off" ></div>
                <div><input type="date" class="form-control pull-right" id="reservation" name="date_from" autocomplete="off" ></div>
                  
                <!-- /.input group -->
                <div class="input-group-ass">
                  <button type="submit" class="btn btn-success">Tìm</button>
                  </div>
                  </form>
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
                <th data-width="100px">Màu sắc</th>
                <th data-width="120px">Số Lượng Đã Bán</th>
                <th data-width="100px" data-type="date-euro">Ngày Bán</th>
              </tr>
            </thead>
            <tbody>
           
            @foreach($product_details as $key=>$product_dt)

                <tr>
                  <td class="text-center">{{$key+1}}</td>
                  <td>
                  <div style="background-image: url('{{ Helper::get_image_product_url($product_dt->image) }}'); padding-top: 100%; background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                  </td>
                  <td>{{ $product_dt->sku_code }}</td>
                  <td>{{ $product_dt->name }}</td>
                  <td>{{$product_dt->color}}</td>
                  <td>{{$product_dt->orderDetailQuantity ? $product_dt->orderDetailQuantity : 0}}</td>
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
    <div class="row no-print">
      <div class="col-xs-12">
        <button class="btn btn-success btn-print pull-right" onclick="printPDF()" ><i class="fa fa-print"></i> In Hóa Đơn</button>
      </div>
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
function printPDF(){
  window.print();
}
</script>


@endsection






