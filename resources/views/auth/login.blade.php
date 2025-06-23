@php
  use Illuminate\Support\Facades\Route;
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="app-brand auth-cover-brand gap-2">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }} PV</span>
    </a>
    <!-- /Logo -->
    <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
      <div class="w-100 d-flex justify-content-center">
      <img src="{{ asset('assets/img/illustrations/boy-with-rocket-' . $configData['theme'] . '.png') }}"
        class="img-fluid" alt="Login image" width="700" data-app-dark-img="illustrations/boy-with-rocket-dark.png"
        data-app-light-img="illustrations/boy-with-rocket-light.png">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-sm-12 mt-8">
      <h4 class="mb-1 text-center fw-bold">{{ config('variables.templateName') }} Password Vault</h4>
      <p class="mb-6 text-center">Masuk untuk mulai manajemen password</p>

      @if (session('status'))
      <div class="alert alert-success mb-1 rounded-0" role="alert">
      <div class="alert-body">
      {{ session('status') }}
      </div>
      </div>
    @endif
      <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-6">
        <label for="login-email" class="form-label">Email</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email"
          placeholder="user@domain.com" autofocus value="{{ old('email') }}" />
        @error('email')
      <span class="invalid-feedback" role="alert">
        <span class="fw-medium">{{ $message }}</span>
      </span>
      @enderror
        </div>
        <div class="form-password-toggle">
        <label class="form-label" for="login-password">Password</label>
        <div class="input-group input-group-merge @error('password') is-invalid @enderror">
          <input type="password" id="login-password" class="form-control @error('password') is-invalid @enderror"
          name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
          aria-describedby="password" />
          <span class="input-group-text cursor-pointer"></span>
        </div>
        @error('password')
      <span class="invalid-feedback" role="alert">
        <span class="fw-medium">{{ $message }}</span>
      </span>
      @enderror
        </div>
        <div class="my-7">
        <div class="d-flex justify-content-between">
          <div class="form-check mb-0">
          <input class="form-check-input" type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }} />
          <label class="form-check-label" for="remember-me">Ingat Saya</label>
          </div>
          @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">
        <p class="mb-0">Lupa Password?</p>
        </a>
      @endif
        </div>
        </div>
        <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
      </form>

      <p class="text-center">
        <span>Belum punya akun?</span>
        @if (Route::has('register'))
      <a href="{{ route('register') }}">
      <span>Buat akun sekarang!</span>
      </a>
      @endif
      </p>

      </div>
    </div>
    <!-- /Login -->
    </div>
  </div>
@endsection