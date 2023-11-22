@extends('layouts.master')

@section('title', 'Quên Mật Khẩu')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('header.Home') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Quên Mật Khẩu</li>
      </ol>
    </nav>
  </section>

  <div class="site-login">
      <div class="login-body">
        <h2 class="title">Quên Mật Khẩu</h2>
        <form action="{{ route('password.email') }}" method="POST" accept-charset="utf-8">
          @csrf

          @if(session('message'))
            <div class="form_message">
              {{ session('message') }}
            </div>
          @endif

          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <button type="submit" class="btn btn-default">Xác Nhận</button>
        </form>
      </div>

      <div class="sign-up-now">
        Not a member? <a href="{{ route('register') }}">Sign up now</a>
      </div>
  </div>
@endsection

@section('css')
  <style>

  </style>
@endsection

@section('js')
  <script>
    $(document).ready(function(){
      @if(session('alert'))
        Swal.fire(
          '{{ session('alert')['title'] }}',
          '{{ session('alert')['content'] }}',
          '{{ session('alert')['type'] }}'
        )
      @endif
    });
  </script>
@endsection
