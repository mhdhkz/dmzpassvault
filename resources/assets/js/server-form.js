$(function () {
  // Inisialisasi Select2
  $('.select2').each(function () {
    var $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih salah satu',
      dropdownParent: $this.parent()
    });
  });

  // Hitung karakter textarea
  const $textarea = $('#multicol-description');
  const $charCount = $('#charCount');
  if ($textarea.length && $charCount.length) {
    $textarea.on('input', function () {
      const remaining = 500 - $(this).val().length;
      $charCount.text(`${remaining} karakter tersisa`);
    });
  }

  // Validasi hostname dan IP
  const $form = $('form');
  const $hostnameInput = $('#multicol-hostname');
  const $ipInput = $('#multicol-ipaddr');
  const token = $('input[name="_token"]').val();

  const $hostnameError = $('<small class="text-danger d-block mt-1" id="hostname-error"></small>');
  const $ipError = $('<small class="text-danger d-block mt-1" id="ip-error"></small>');

  $hostnameInput.after($hostnameError);
  $ipInput.after($ipError);

  let hostnameTimer, ipTimer;
  const ipv4Regex = /^(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})(\.(25[0-5]|2[0-4][0-9]|1?[0-9]{1,2})){3}$/;

  // Cek duplikasi hostname
  $hostnameInput.on('input', function () {
    clearTimeout(hostnameTimer);
    const hostname = $(this).val().trim();
    if (hostname !== '') {
      hostnameTimer = setTimeout(() => {
        $.post('/server/check-hostname', { hostname, _token: token }, function (data) {
          if (data.exists) {
            $hostnameError.text('Hostname sudah digunakan, silakan pilih nama lain.');
          } else {
            $hostnameError.text('');
          }
        });
      }, 500);
    } else {
      $hostnameError.text('');
    }
  });

  // Cek format dan duplikasi IP
  $ipInput.on('input', function () {
    clearTimeout(ipTimer);
    const ip = $(this).val().trim();

    if (ip && !ipv4Regex.test(ip)) {
      $ipError.text('Format IP Address tidak valid! Contoh: 192.168.1.1');
      return;
    }

    if (ip !== '') {
      ipTimer = setTimeout(() => {
        $.post('/server/check-ip', { ip_addr_srv: ip, _token: token }, function (data) {
          if (data.exists) {
            $ipError.text('IP Address sudah digunakan.');
          } else {
            $ipError.text('');
          }
        });
      }, 500);
    } else {
      $ipError.text('');
    }
  });

  // Validasi akhir saat submit
  $form.on('submit', function (e) {
    const hostnameError = $hostnameError.text().trim();
    const ip = $ipInput.val().trim();
    const ipFormatInvalid = ip && !ipv4Regex.test(ip);
    const ipErrorText = $ipError.text().trim();

    if (hostnameError !== '') {
      e.preventDefault();
      $hostnameInput.focus();
      return;
    }

    if (ipFormatInvalid) {
      e.preventDefault();
      $ipError.text('Format IP Address tidak valid! Contoh: 192.168.1.1');
      $ipInput.focus();
      return;
    }

    if (ipErrorText !== '') {
      e.preventDefault();
      $ipInput.focus();
      return;
    }
  });
});
