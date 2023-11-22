@extends('admin.layouts.master')

@section('title', 'Tạo Bài Viết Mới')

@section('embed-css')

@endsection

@section('custom-css')

@endsection

@section('breadcrumb')
<ol class="breadcrumb">
  <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
  <li><a href="{{ route('admin.post.index') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Quản Lý Bài Viết</a></li>
  <li class="active">Tạo Bài Viết Mới</li>
</ol>
@endsection

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

<form action="{{ route('admin.post.save') }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-body">
          <div class="form-group">
            <label for="title">Tiêu Đề <span class="text-red">*</span></label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Tiêu đề bài viết" required value="{{ old('title') }}" autocomplete="off">
          </div>
          <div class="form-group">
            <label>Nội Dung <span class="text-red">*</span></label>
            <textarea id="post-content" name="content" rows="20">{{ old('content') }}</textarea>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border"><b>Hình Ảnh</b> <span class="text-red">*</span></div>
        <div class="box-body">
          <div class="upload-image text-center">
            <div title="Image Preview" class="image-preview" style="background-image: url('{{ Helper::get_image_post_url() }}'); padding-top: 50%; background-size: contain; background-repeat: no-repeat; background-position: center; margin-bottom: 5px; border: 1px solid #f4f4f4;"></div>
            <label for="upload" title="Upload Image" class="btn btn-primary btn-sm"><i class="fa fa-folder-open"></i>Chọn Hình Ảnh</label>
            <input type="file" accept="image/*" id="upload" style="display:none" name="image">
          </div>
        </div>
      </div>
      <div class="box box-primary">
        <div class="box-header with-border"><b>Đăng Bài</b></div>
        <div class="box-body">
          <a href="{{ route('admin.post.index') }}" class="btn btn-danger btn-flat"><i class="fa fa-ban" aria-hidden="true"></i> Hủy</a>
          <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('embed-js')

<!-- include tinymce js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.15/tinymce.min.js"></script>
@endsection

@section('custom-js')
<script>
  tinymce.init({
    selector: 'textarea#post-content',
    plugins: 'media image code table link lists preview fullscreen',
    toolbar: 'undo redo | formatselect | fontsizeselect | bold italic underline forecolor | alignleft aligncenter alignright alignjustify | numlist bullist | outdent indent | link image media table | code preview fullscreen',
    toolbar_drawer: 'sliding',
    entity_encoding : "raw",
    branding: false,
    /* enable title field in the Image dialog*/
    image_title: true,
    height: 400,
    min_height: 300,
    /* Link Custom */
    link_assume_external_targets: 'http',
    /* disable media advanced tab */
    media_alt_source: false,
    media_poster: false,
    /* enable automatic uploads of images represented by blob or data URIs*/
    automatic_uploads: true,
    /*
      URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
      images_upload_url: 'postAcceptor.php',
      here we add custom filepicker only to Image dialog
    */
    file_picker_types: 'image',
    /* and here's our custom image picker*/
    file_picker_callback: function (cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      /*
        Note: In modern browsers input[type="file"] is functional without
        even adding it to the DOM, but that might not be the case in some older
        or quirky browsers like IE, so you might want to add it to the DOM
        just in case, and visually hide it. And do not forget do remove it
        once you do not need it anymore.
      */

      input.onchange = function () {
        var file = this.files[0];

        var reader = new FileReader();
        reader.onload = function () {
          /*
            Note: Now we need to register the blob in TinyMCEs image blob
            registry. In the next release this part hopefully won't be
            necessary, as we are looking to handle it internally.
          */
          var id = 'blobid' + (new Date()).getTime();
          var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);

          /* call the callback and populate the Title field with the file name */
          cb(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };

      input.click();
    }
  });

  $(document).ready(function(){
    $("#upload").change(function() {
      $('.upload-image .image-preview').css('background-image', 'url("' + getImageURL(this) + '")');
    });
  });

  function getImageURL(input) {
    return URL.createObjectURL(input.files[0]);
  };
</script>
@endsection
