@extends('layouts.master')

@section('title', 'Đăng Ký')

@section('content')
  <section class="bread-crumb">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Register') }}</li>
      </ol>
    </nav>
  </section>
  <div class="site-register">
    <div class="register-body">
      <h2 class="title">Đăng Ký</h2>
      <form action="{{ route('register') }}" method="POST" accept-charset="utf-8">
        @csrf

        @if(session('message'))
          <div class="form_message">
            {{ session('message') }}
          </div>
        @endif

        <div class="input-group">
          <span class="input-group-addon"><i class="fas fa-user"></i></span>
          <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name" value="{{ old('name') }}" required autocomplete="name" autofocus>

          @error('name')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email">

          @error('email')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fas fa-mobile"></i></span>
          <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Phone" value="{{ old('phone') }}" required autocomplete="phone">

          @error('phone')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="fas fa-lock"></i></span>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="new-password">

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

        <button type="submit" class="btn btn-default">REGISTER</button>
      </form>
    </div>
    <div class="register-social">
      <div class="register-social-text">Or Register With</div>
      <div class="row">
        <div class="col-md-6">
          <a href="#" title="Facebook" class="btn btn-defaule"><i class="fab fa-facebook-square"></i> Facebook</a>
        </div>
        <div class="col-md-6">
          <a href="#" title="Google" class="btn btn-defaule"><i class="fab fa-google"></i> Google</a>
        </div>
      </div>
    </div>
    <div class="sign-in-now">
      You are a member? <a href="{{ route('login') }}">Sign in now</a>
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
