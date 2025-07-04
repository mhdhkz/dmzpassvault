@php
  use Illuminate\Support\Facades\Route;
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Daftar')

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
      <img src="{{ asset('assets/img/illustrations/girl-with-laptop-' . $configData['theme'] . '.png') }}"
        class="img-fluid scaleX-n1-rtl" alt="Login image" width="700"
        data-app-dark-img="illustrations/girl-with-laptop-dark.png"
        data-app-light-img="illustrations/girl-with-laptop-light.png" />
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Register -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-sm-12 mt-8">
      <h4 class="mb-1 text-center fw-bold">DMZ Password Vault</h4>
      <p class="mb-6 text-center">Daftar sekarang untuk mulai manajemen password</p>

      <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-6">
        <label for="username" class="form-label">Nama</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="username" name="name"
          placeholder="Nama Anda" autofocus value="{{ old('name') }}" />
        @error('name')
      <span class="invalid-feedback" role="alert">
        <span class="fw-medium">{{ $message }}</span>
      </span>
      @enderror
        </div>
        <div class="mb-6">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
          placeholder="user@domain.com" value="{{ old('email') }}" />
        @error('email')
      <span class="invalid-feedback" role="alert">
        <span class="fw-medium">{{ $message }}</span>
      </span>
      @enderror
        </div>
        <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password">Password</label>
        <div class="input-group input-group-merge @error('password') is-invalid @enderror">
          <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
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
        <div class="mb-6 form-password-toggle">
        <label class="form-label" for="password-confirm">Konfirmasi Password</label>
        <div class="input-group input-group-merge">
          <input type="password" id="password-confirm" class="form-control" name="password_confirmation"
          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
          aria-describedby="password" />
          <span class="input-group-text cursor-pointer"></span>
        </div>
        </div>
        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
        <div class="my-7">
        <div class="form-check @error('terms') is-invalid @enderror">
        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms"
        name="terms" />
        <label class="form-check-label" for="terms">
        I agree to
        <a href="{{ route('policy.show') }}" target="_blank">privacy policy</a> &
        <a href="{{ route('terms.show') }}" target="_blank">terms</a>
        </label>
        </div>
        @error('terms')
      <div class="invalid-feedback" role="alert">
        <!--<span class="fw-medium">{{ $message }}</span>-->
      </div>
      @enderror
        </div>
      @endif
        <button type="submit" class="btn btn-primary d-grid w-100">Daftar</button>
      </form>

      <p class="text-center">
        <span>Sudah punya akun?</span>
        @if (Route::has('login'))
      <a href="{{ route('login') }}">
      <span>Login sekarang!</span>
      </a>
      @endif
      </p>

      </div>
    </div>
    <!-- /Register -->
    </div>
  </div>
@endsection
