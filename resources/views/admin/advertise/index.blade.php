@extends('admin.layouts.master')

@section('title', 'Quản Lý Quảng Cáo')

@section('embed-css')
<link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('custom-css')
<style>
  #advertise-table td,
  #advertise-table th {
    vertical-align: middle !important;
  }
  #advertise-table span.status-label {
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
  <li class="active">Quản Lý Quảng Cáo</li>
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
                <a href="{{ route('admin.advertise.index') }}" class="btn btn-flat btn-primary" title="Refresh" style="margin-right: 5px;">
                  <i class="fa fa-refresh"></i><span class="hidden-xs"> Refresh</span>
                </a>
                <a href="{{ route('admin.advertise.new') }}" class="btn btn-success btn-flat" title="Thêm Mới">
                  <i class="fa fa-plus" aria-hidden="true"></i><span class="hidden-xs"> Thêm Mới</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <table id="advertise-table" class="table table-hover" style="width:100%; min-width: 768px;">
            <thead>
              <tr>
                <th data-width="10px">ID</th>
                <th data-orderable="false" data-width="100px">Hình Ảnh</th>
                <th data-orderable="false">Tiêu Đề</th>
                <th data-orderable="false" data-width="85px">Hiển Thị</th>
                <th data-width="60px" data-type="date-euro">Ngày Tạo</th>
                <th data-width="66px">Trạng Thái</th>
                <th data-orderable="false" data-width="70px">Tác Vụ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($advertises as $advertise)
                <tr>
                  <td class="text-center">
                    {{ $advertise->id }}
                  </td>
                  <td>
                    <div style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}'); padding-top: 50%; background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                  </td>
                  <td>
                    <a href="javascript:void(0);" class="text-left" title="{{ $advertise->title }}">{{ $advertise->title }}</a>
                  </td>
                  <td>
                    @if($advertise->at_home_page)
                      Trang Chủ
                    @else
                      Trang Thường
                    @endif
                  </td>
                  <td> {{ \Carbon\Carbon::parse($advertise->created_at)->format('d/m/Y')}}</td>
                  <td>
                    @if($advertise->start_date <= date('Y-m-d') && $advertise->end_date >= date('Y-m-d'))
                      <span class="label-success status-label">Active</span>
                    @else
                      <span class="label-danger status-label">Inactive</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.advertise.edit', ['id' => $advertise->id]) }}" class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                      <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                    <a href="javascript:void(0);" data-id="{{ $advertise->id }}" class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa" data-url="{{ route('admin.advertise.delete') }}">
                      <i class="fa fa-trash"></i>
                    </a>
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
    var table = $('#advertise-table').DataTable({
      "language": {
        "zeroRecords":    "Không tìm thấy kết quả phù hợp",
        "info":           "Hiển thị trang <b>_PAGE_/_PAGES_</b> của <b>_TOTAL_</b> hình ảnh quảng cáo",
        "infoEmpty":      "Hiển thị trang <b>1/1</b> của <b>0</b> hình ảnh quảng cáo",
        "infoFiltered":   "(Tìm kiếm từ <b>_MAX_</b> hình ảnh quảng cáo)",
        "emptyTable": "Không có dữ liệu hình ảnh quảng cáo",
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

  $(document).ready(function(){

    $(".deleteDialog").click(function() {

      var advertise_id = $(this).attr('data-id');
      var url = $(this).attr('data-url');

      Swal.fire({
        type: 'question',
        title: 'Thông báo',
        text: 'Bạn có chắc muốn xóa quảng cáo này?',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return fetch(url, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({'advertise_id': advertise_id}),
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(response.statusText);
            }
            return response.json();
          })
          .catch(error => {
            Swal.showValidationMessage(error);

            Swal.update({
              type: 'error',
              title: 'Lỗi!',
              text: '',
              showConfirmButton: false,
              cancelButtonText: 'Ok',
            });
          })
        },
      }).then((result) => {
        if (result.value) {
          Swal.fire({
            type: result.value.type,
            title: result.value.title,
            text: result.value.content,
          }).then((result) => {
            if (result.value)
              location.reload(true);
          });
        }
      })
    });
  });
</script>
@endsection
