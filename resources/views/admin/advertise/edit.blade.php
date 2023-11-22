@extends('admin.layouts.master')

@section('title', 'Chỉnh Sửa Quảng Cáo')

@section('embed-css')
  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection

@section('custom-css')

@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li><a href="{{ route('admin.advertise.index') }}"><i class="fa fa-sliders" aria-hidden="true"></i> Quản Lý Quảng Cáo</a></li>
  <li class="active">Chỉnh Sửa Quảng Cáo</li>
</ol>
@endsection

/* `@section('content')` is a Blade directive in Laravel that defines a section of the view that will
be replaced by the content passed from the controller. In this case, it is defining the main content
section of the view for editing an advertisement. */
@section('content')

@if ($errors->any())
  <div class="callout callout-danger">
    <h4>Warning!</h4>
    <ul style="margin-bottom: 0;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ route('admin.advertise.update', ['id' => $advertise->id]) }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
  @csrf
  <div class="box box-primary">
    <div class="box-body">
      <div class="row">
        <div class="col-md-3">
          <label for="title">Hình Ảnh <span class="text-red">*</span></label>
          <div class="upload-image text-center">
            <div title="Image Preview" class="image-preview" style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}'); padding-top: 45%; background-size: contain; background-repeat: no-repeat; background-position: center; margin-bottom: 5px; border: 1px solid #f4f4f4;"></div>
            <label for="upload" title="Upload Image" class="btn btn-primary btn-sm"><i class="fa fa-folder-open"></i>Chọn Hình Ảnh</label>
            <input type="file" accept="image/*" id="upload" style="display:none" name="image">
          </div>
        </div>
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">Tiêu Đề <span class="text-red">*</span></label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Tiêu đề quảng cáo" value="{{ old('title') ?: $advertise->title }}" autocomplete="off">
          </div>
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                  <label>Vị Trí Hiển Thị <span class="text-red">*</span></label>
                  <select class="form-control" name="at_home_page">
                    <option value="0"  @if( old('at_home_page') == NULL ) {{ $advertise->at_home_page ? '' : 'selected' }} @else {{ old('at_home_page') ? '' : 'selected' }} @endif>Trang Thường</option>
                    <option value="1"  @if( old('at_home_page') == NULL ) {{ $advertise->at_home_page ? 'selected' : '' }} @else {{ old('at_home_page') ? 'selected' : '' }} @endif>Trang Chủ</option>
                  </select>
                </div>
            </div>
            <div class="col-md-7">
              <!-- Date range -->
              <div class="form-group">
                <label for="reservation">Ngày Bắt Đầu - Ngày Kết Thúc <span class="text-red">*</span></label>

                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right" id="reservation" name="date" autocomplete="off" value="{{ old('date') ?: date_format(date_create($advertise->start_date), 'd/m/Y').' - '.date_format(date_create($advertise->end_date), 'd/m/Y') }}">
                </div>
                <!-- /.input group -->
              </div>
              <!-- /.form group -->
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu</button>
            <a href="{{ route('admin.advertise.index') }}" class="btn btn-danger btn-flat pull-right" style="margin-right: 5px;"><i class="fa fa-ban" aria-hidden="true"></i> Hủy</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('embed-js')

<!-- date-range-picker -->
<script src="{{ asset('AdminLTE/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('custom-js')
<script>
  $(document).ready(function(){
    //Date range picker
    $('#reservation').daterangepicker({
      autoApply: true,
      minDate: "{{ date_format(date_create($advertise->start_date), 'd/m/Y') }}",
      "locale": {
        "format": "DD/MM/YYYY",
      }
    });

    $("#upload").change(function() {
      $('.upload-image .image-preview').css('background-image', 'url("' + getImageURL(this) + '")');
    });
  });

  function getImageURL(input) {
    return URL.createObjectURL(input.files[0]);
  };
</script>
@endsection
