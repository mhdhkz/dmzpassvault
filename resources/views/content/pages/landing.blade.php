@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Landing Page')

<!-- Page Styles -->
@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/front-page-landing.js'])
@endsection


@section('content')
  <div data-bs-spy="scroll" class="scrollspy-example">
    <!-- Contact Us: Start -->
    <section id="landingContact" class="section-py bg-body landing-contact">
    <div class="container">
      <h4 class="text-center mb-1 mt-10">
      <span class="position-relative fw-extrabold z-1">Satu Tempat,
        <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="laptop charging"
        class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
      </span>
      Kata Sandi Aman
      </h4>
      <p class="text-center mb-12 pb-md-4">Solusi Manajemen Kata Sandi Terpusat dan Terintegrasi</p>
      <div class="row g-6">
      <div class="col-lg-5">
        <div class="contact-img-box position-relative border p-2 h-100">
        <img src="{{ asset('assets/img/front-pages/icons/contact-border.png') }}" alt="contact border"
          class="contact-border-img position-absolute d-none d-lg-block scaleX-n1-rtl" />
        <img src="{{ asset('assets/img/front-pages/landing-page/contact-customer-service.png') }}"
          alt="contact customer service" class="contact-img w-100 scaleX-n1-rtl" />
        <div class="p-4 pb-2">
          <div class="row g-4">
          <div class="col-md-6 col-lg-12 col-xl-6">
            <div class="d-flex align-items-center">
            <div class="badge bg-label-warning rounded p-1_5 me-3"><i
              class="icon-base bx bx-building icon-lg"></i></div>
            <div>
              <p class="mb-0">Organisasi</p>
              <h6 class="mb-0"><a href="https://grahakarya.com/" class="text-heading">PT Graha Karya
                Informasi</a></h6>
            </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-12 col-xl-6">
            <div class="d-flex align-items-center">
            <div class="badge bg-label-success rounded p-1_5 me-3"><i
              class="icon-base bx bx-phone-call icon-lg"></i></div>
            <div>
              <p class="mb-0">Telepon</p>
              <h6 class="mb-0"><a href="tel:+622130066518" class="text-heading">02130066518</a></h6>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="card h-100">
        <div class="card-body">
          <h4 class="text-center fw-bold mt-7 mb-2">DMZ Password Vault</h4>
          <p class="text-center mb-6">
          Sudah jadi bagian dari kami? Langsung login.<br class="d-none d-lg-block" />
          Baru pertama kali? Yuk, daftar dan mulai kelola password-mu dengan mudah.
          </p>
          <div class="row g-4">
          <div class="col-12">
            <div class="row g-4 justify-content-center text-center">
            <!-- Register -->
            <div class="col-12 col-md-6 col-lg-5">
              <img src="{{ asset('assets/img/front-pages/landing-page/daftar-illustration.png') }}"
              alt="Ilustrasi Daftar" class="img-fluid mb-3" />
              <a href="{{ url('/register') }}" class="btn btn-secondary w-100">
              <i class="bx bx-user-plus me-1"></i> Daftar
              </a>
            </div>

            <!-- Login -->
            <div class="col-12 col-md-6 col-lg-5">
              <img src="{{ asset('assets/img/front-pages/landing-page/login-illustration.png') }}"
              alt="Ilustrasi Login" class="img-fluid mb-3" />
              <a href="{{ url('/login') }}" class="btn btn-primary w-100">
              <i class="bx bx-log-in-circle me-1"></i> Login
              </a>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </section>
    <!-- Contact Us: End -->
  </div>
@endsection