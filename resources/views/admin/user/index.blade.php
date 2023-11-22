@extends('admin.layouts.master')

@section('title', 'Quản Lý Tài Khoản')

@section('embed-css')
<link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('custom-css')
<style>
  #user-table td,
  #user-table th {
    vertical-align: middle !important;
  }
  #user-table span.status-label {
    display: block;
    width: 85px;
    text-align: center;
    padding: 2px 0px;
  }
  #new-account-modal .modal-dialog {
    max-width: 480px;
  }
  #new-account-modal .input-group {
    width: 100%;
    margin: 0 auto;
    margin-bottom: 10px;
  }
  #new-account-modal span.input-group-addon,
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
  #new-account-modal span.input-group-addon i,
  #search-input span.input-group-addon i {
    font-size: 18px;
    line-height: 34px;
    width: 34px;
    color: #f30;
  }
  #new-account-modal input,
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
  #new-account-modal span.invalid-feedback {
    width: 100%;
    font-size: 14px;
    color: #ff0000;
    display: block;
    margin-top: .3rem;
  }
</style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li class="active">Quản Lý Tài Khoản</li>
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
                <a href="{{ route('admin.users') }}" class="btn btn-flat btn-primary" title="Refresh" style="margin-right: 5px;">
                  <i class="fa fa-refresh"></i><span class="hidden-xs"> Refresh</span>
                </a>
                <a href="#new-account-modal" class="btn btn-success btn-flat" data-toggle="modal" title="New Account">
                  <i class="fa fa-user-plus" aria-hidden="true"></i><span class="hidden-xs"> New Account</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <table id="user-table" class="table table-hover" style="width:100%; min-width: 920px;">
            <thead>
              <tr>
                <th data-width="10px">ID</th>
                <th data-orderable="false" data-width="60px">Hình Ảnh</th>
                <th data-orderable="false" data-width="100px">Tên Tài Khoản</th>
                <th data-orderable="false">Email</th>
                <th data-orderable="false" data-width="85px">Số Điện Thoại</th>
                <th data-orderable="false">Địa Chỉ</th>
                <th data-width="60px" data-type="date-euro">Ngày Tạo</th>
                <th data-width="66px">Trạng Thái</th>
                <th data-orderable="false" data-width="70px">Tác Vụ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td class="text-center">
                    {{ $user->id }}
                  </td>
                  <td>
                    <div style="background-image: url('{{ Helper::get_image_avatar_url($user->avatar_image) }}'); padding-top: 100%; background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                  </td>
                  <td>
                    <a class="text-left" href="{{ route('admin.user_show', ['id' => $user->id]) }}" title="{{ $user->name }}">{{ $user->name }}</a>
                  </td>
                  <td> {{ $user->email }} </td>
                  <td> {{ $user->phone }} </td>
                  <td> {{ $user->address }} </td>
                  <td> {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y')}}</td>
                  <td>
                    @if($user->active)
                      <span class="label-success status-label">Activated</span>
                    @else
                      <span class="label-danger status-label">Not Activated</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.user_show', ['id' => $user->id]) }}" class="btn btn-icon btn-sm btn-primary tip" title="Chi tiết">
                      <i class="fa fa-eye"></i>
                    </a>
                    @if(!$user->active)
                      <a href="javascript:void(0);" data-id="{{ $user->id }}" class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa" data-url="{{ route('admin.user_delete') }}">
                        <i class="fa fa-trash"></i>
                      </a>
                    @endif
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

  <div class="modal fade" id="new-account-modal">
    <div class="modal-dialog">
      <div class="modal-content box" style="margin-bottom: 0; border: none; box-shadow: none; border-radius: none;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Thêm Tài Khoản Mới</h4>
        </div>
        <div class="modal-body">
          <form action="{{ route('admin.user_new') }}" method="POST" accept-charset="utf-8">
            @csrf
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>

              @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-success">
                <i class="fa fa-user-plus" aria-hidden="true"></i> Add Account
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
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
    var table = $('#user-table').DataTable({
      "language": {
        "zeroRecords":    "Không tìm thấy kết quả phù hợp",
        "info":           "Hiển thị trang <b>_PAGE_/_PAGES_</b> của <b>_TOTAL_</b> tài khoản",
        "infoEmpty":      "Hiển thị trang <b>1/1</b> của <b>0</b> tài khoản",
        "infoFiltered":   "(Tìm kiếm từ <b>_MAX_</b> tài khoản)",
        "emptyTable": "Không có dữ liệu tài khoản",
      },
      "lengthChange": false,
       "autoWidth": false,
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
    $('#new-account-modal form').on('submit', function(e) {
      e.preventDefault();
      $('#new-account-modal span.invalid-feedback').remove();
      $("#new-account-modal .modal-content").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      var formData = $(this).serialize();
      var method = $(this).attr('method');
      var url = $(this).attr('action');
      $.ajax({
        url: url,
        type: method,
        data: formData,
        dataType: 'JSON',
        success: function(data) {
          $("#new-account-modal .modal-content .overlay").remove();
          Swal.fire(
            data.title,
            data.content,
            data.type
          ).then((result) => {
            if (result.value)
              location.reload(true);
          });
        },
        error: function(data) {
          $("#new-account-modal .modal-content .overlay").remove();
          var errors = data.responseJSON;
          $.each(errors, function (key, value) {
            $('#new-account-modal input[name="'+key+'"]').after('<span class="invalid-feedback" role="alert">' + value + '</span>');
          });
        }
      });
    });

    $(".deleteDialog").click(function() {

      var user_id = $(this).attr('data-id');
      var url = $(this).attr('data-url');

      Swal.fire({
        type: 'question',
        title: 'Thông báo',
        text: 'Bạn có chắc muốn xóa tài khoản này?',
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
            body: JSON.stringify({'user_id': user_id}),
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
