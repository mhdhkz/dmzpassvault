<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img src="{{asset('assets/img/front-pages/backgrounds/footer-bg.png')}}" alt="footer bg"
      class="footer-bg banner-bg-img z-n1" />
    <div class="container">
      <div class="row gx-0 gy-6 g-lg-10">
        <div class="col-lg-5">
          <a href="{{url('front-pages/landing')}}" class="app-brand-link mb-6">
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
            <span class="app-brand-text demo text-white fw-bold ms-2 ps-1">{{ config('variables.templateName') }}
              PV</span>
          </a>
          <p class="footer-text footer-logo-description mb-6">Aplikasi Manajemen
            Kata Sandi Terpusat dan Terintegrasi.
          </p>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title fw-bold mb-6">Link Profil</h6>
          <ul class="list-unstyled">
            <li class="mb-4">
              <a href="https://www.linkedin.com/in/dhika-mahendra-sudrajat" target="_blank"
                class="footer-link">LinkedIn</a>
            </li>
            <li class="mb-4">
              <a href="https://github.com/mhdhkz/" target="_blank" class="footer-link">Github</a>
            </li>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text">©
          <script>
            document.write(new Date().getFullYear());
          </script>
          <span class="footer-bottom-text">Copyright: </span>
        </span>
        <a href="https://github.com/mhdhkz"><b>DMZ</b></a> | All rights reserved.
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->