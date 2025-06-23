@extends('layouts/layoutMaster')

@section('title', 'Form Server')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/form-wizard-icons.js'])
@endsection

@section('content')
  <!-- Default -->
  <div class="row">
    <div class="col-12">
    <h5>Default</h5>
    </div>
    <!-- Modern Vertical Icons Wizard -->
    <div class="col-12 mb-4">
    <small class="fw-medium">Vertical Icons</small>
    <div class="bs-stepper vertical wizard-modern wizard-modern-vertical-icons-example mt-2">
      <div class="bs-stepper-header">
      <div class="step" data-target="#account-details-vertical-modern">
        <button type="button" class="step-trigger">
        <span class="bs-stepper-circle">
          <i class="icon-base bx bx-detail"></i>
        </span>
        <span class="bs-stepper-label">
          <span class="bs-stepper-title">Account Details</span>
          <span class="bs-stepper-subtitle">Setup Account Details</span>
        </span>
        </button>
      </div>
      <div class="line"></div>
      <div class="step" data-target="#personal-info-vertical-modern">
        <button type="button" class="step-trigger">
        <span class="bs-stepper-circle">
          <i class="icon-base bx bx-user"></i>
        </span>
        <span class="bs-stepper-label">
          <span class="bs-stepper-title">Personal Info</span>
          <span class="bs-stepper-subtitle">Add personal info</span>
        </span>
        </button>
      </div>
      <div class="line"></div>
      <div class="step" data-target="#social-links-vertical-modern">
        <button type="button" class="step-trigger">
        <span class="bs-stepper-circle">
          <i class="icon-base bx bxl-instagram"></i>
        </span>
        <span class="bs-stepper-label">
          <span class="bs-stepper-title">Social Links</span>
          <span class="bs-stepper-subtitle">Add social links</span>
        </span>
        </button>
      </div>
      </div>
      <div class="bs-stepper-content">
      <form onSubmit="return false">
        <!-- Account Details -->
        <div id="account-details-vertical-modern" class="content">
        <div class="content-header mb-4">
          <h6 class="mb-0">Account Details</h6>
          <small>Enter Your Account Details.</small>
        </div>
        <div class="row g-6">
          <div class="col-sm-6">
          <label class="form-label" for="username-modern-vertical">Username</label>
          <input type="text" id="username-modern-vertical" class="form-control" placeholder="john.doe" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="email-modern-vertical">Email</label>
          <input type="text" id="email-modern-vertical" class="form-control" placeholder="john.doe"
            aria-label="john.doe" />
          </div>
          <div class="col-sm-6 form-password-toggle">
          <label class="form-label" for="password-modern-vertical">Password</label>
          <div class="input-group input-group-merge">
            <input type="password" id="password-modern-vertical" class="form-control"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            aria-describedby="password-modern-vertical1" />
            <span class="input-group-text cursor-pointer" id="password-modern-vertical1"><i
              class="icon-base bx bx-hide"></i></span>
          </div>
          </div>
          <div class="col-sm-6 form-password-toggle">
          <label class="form-label" for="confirm-password-modern-vertical2">Confirm Password</label>
          <div class="input-group input-group-merge">
            <input type="password" id="confirm-password-modern-vertical2" class="form-control"
            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
            aria-describedby="confirm-password-modern-vertical3" />
            <span class="input-group-text cursor-pointer" id="confirm-password-modern-vertical3"><i
              class="icon-base bx bx-hide"></i></span>
          </div>
          </div>
          <div class="col-12 d-flex justify-content-between">
          <button class="btn btn-label-secondary btn-prev" disabled>
            <i class="icon-base bx bx-left-arrow-alt scaleX-n1-rtl icon-sm ms-sm-n2 me-sm-2"></i>
            <span class="align-middle d-sm-inline-block d-none">Previous</span>
          </button>
          <button class="btn btn-primary btn-next">
            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
            <i class="icon-base bx bx-right-arrow-alt scaleX-n1-rtl icon-sm me-sm-n2"></i>
          </button>
          </div>
        </div>
        </div>
        <!-- Personal Info -->
        <div id="personal-info-vertical-modern" class="content">
        <div class="content-header mb-4">
          <h6 class="mb-0">Personal Info</h6>
          <small>Enter Your Personal Info.</small>
        </div>
        <div class="row g-6">
          <div class="col-sm-6">
          <label class="form-label" for="first-name-modern-vertical">First Name</label>
          <input type="text" id="first-name-modern-vertical" class="form-control" placeholder="John" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="last-name-modern-vertical">Last Name</label>
          <input type="text" id="last-name-modern-vertical" class="form-control" placeholder="Doe" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="country-vertical-modern">Country</label>
          <select class="select2" id="country-vertical-modern">
            <option label=" "></option>
            <option>UK</option>
            <option>USA</option>
            <option>Spain</option>
            <option>France</option>
            <option>Italy</option>
            <option>Australia</option>
          </select>
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="language-vertical-modern">Language</label>
          <select class="selectpicker w-auto" id="language-vertical-modern" data-style="btn-default"
            data-icon-base="bx" data-tick-icon="bx-check text-white" multiple>
            <option>English</option>
            <option>French</option>
            <option>Spanish</option>
          </select>
          </div>
          <div class="col-12 d-flex justify-content-between">
          <button class="btn btn-primary btn-prev">
            <i class="icon-base bx bx-left-arrow-alt scaleX-n1-rtl icon-sm ms-sm-n2 me-sm-2"></i>
            <span class="align-middle d-sm-inline-block d-none">Previous</span>
          </button>
          <button class="btn btn-primary btn-next">
            <span class="align-middle d-sm-inline-block d-none me-sm-2">Next</span>
            <i class="icon-base bx bx-right-arrow-alt scaleX-n1-rtl icon-sm me-sm-n2"></i>
          </button>
          </div>
        </div>
        </div>
        <!-- Social Links -->
        <div id="social-links-vertical-modern" class="content">
        <div class="content-header mb-4">
          <h6 class="mb-0">Social Links</h6>
          <small>Enter Your Social Links.</small>
        </div>
        <div class="row g-6">
          <div class="col-sm-6">
          <label class="form-label" for="twitter-vertical-modern">Twitter</label>
          <input type="text" id="twitter-vertical-modern" class="form-control"
            placeholder="https://twitter.com/abc" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="facebook-vertical-modern">Facebook</label>
          <input type="text" id="facebook-vertical-modern" class="form-control"
            placeholder="https://facebook.com/abc" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="google-vertical-modern">Google+</label>
          <input type="text" id="google-vertical-modern" class="form-control"
            placeholder="https://plus.google.com/abc" />
          </div>
          <div class="col-sm-6">
          <label class="form-label" for="linkedin-vertical-modern">Linkedin</label>
          <input type="text" id="linkedin-vertical-modern" class="form-control"
            placeholder="https://linkedin.com/abc" />
          </div>
          <div class="col-12 d-flex justify-content-between">
          <button class="btn btn-primary btn-prev">
            <i class="icon-base bx bx-left-arrow-alt scaleX-n1-rtl icon-sm ms-sm-n2 me-sm-2"></i>
            <span class="align-middle d-sm-inline-block d-none">Previous</span>
          </button>
          <button class="btn btn-success btn-submit">Submit</button>
          </div>
        </div>
        </div>
      </form>
      </div>
    </div>
    </div>
    <!-- /Modern Vertical Icons Wizard -->
  </div>
@endsection
