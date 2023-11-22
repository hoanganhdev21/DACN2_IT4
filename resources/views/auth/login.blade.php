@extends('layouts.master')

@section('title', 'Đăng Nhập')

@section('content')

  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Login') }}</li>
      </ol>
    </nav>
  </section>

  <div class="site-login">
      <div class="login-body">
        <h2 class="title">Đăng Nhập</h2>
        <form action="{{ route('login') }}" method="POST" accept-charset="utf-8">
          @csrf

          @if(session('message'))
            <div class="form_message">
              {{ session('message') }}
            </div>
          @endif

          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-user"></i></span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">

            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="login-form-group">
            <div class="row">
              <div class="col-md-6">
                <div class="checkbox">
                  <label><input name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>Remember me</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="forgot-password">
                    <a href="{{ route('password.request') }}" title="Forgot password">Forgot password</a>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-default">LOGIN</button>
        </form>
      </div>
      <div class="login-social">
        <div class="login-social-text">Or login with</div>
        <div class="row">
          <div class="col-md-6">
            <a href="#" title="Facebook" class="btn btn-defaule"><i class="fab fa-facebook-square"></i> Facebook</a>
          </div>
          <div class="col-md-6">
            <a href="#" title="Google" class="btn btn-defaule"><i class="fab fa-google"></i> Google</a>
          </div>
        </div>
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
