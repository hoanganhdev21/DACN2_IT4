@extends('layouts.master')

@section('title', 'Đặt Lại Mật Khẩu')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('header.Home') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Đặt Lại Mật Khẩu</li>
      </ol>
    </nav>
  </section>

  <div class="site-login">
      <div class="login-body">
        <h2 class="title">Đặt Lại Mật Khẩu</h2>
        <form action="{{ route('password.update') }}" method="POST" accept-charset="utf-8">
          @csrf

          @if(session('message'))
            <div class="form_message">
              {{ session('message') }}
            </div>
          @endif
          <input type="hidden" name="token" value="{{ $token }}">
          <input type="hidden" name="email" value="{{ $email }}">
          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
            <input disabled id="email" type="email" class="form-control" name="email" placeholder="Email" value="{{ $email ?? old('email') }}" required autocomplete="email">

            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="new-password" autofocus>

            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-lock"></i></span>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Password Confirmation" required autocomplete="new-password">
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
