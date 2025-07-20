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
  const ipv4Regex = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;

  $hostnameInput.on('input', function () {
    clearTimeout(hostnameTimer);
    const hostname = $(this).val().trim();
    if (hostname !== '') {
      hostnameTimer = setTimeout(() => {
        $.post('/identity/check-hostname', { hostname, _token: token }, function (data) {
          $hostnameError.text(data.exists ? 'Hostname sudah didaftarkan, silakan pilih nama lain.' : '');
        });
      }, 500);
    } else {
      $hostnameError.text('');
    }
  });

  $ipInput.on('input', function () {
    clearTimeout(ipTimer);
    const ip = $(this).val().trim();

    if (ip && !ipv4Regex.test(ip)) {
      $ipError.text('Format IP Address tidak valid! Contoh: 192.168.1.1');
      return;
    } else {
      $ipError.text('');
    }

    if (ip !== '') {
      ipTimer = setTimeout(() => {
        $.post('/identity/check-ip', { ip_addr_srv: ip, _token: token }, function (data) {
          $ipError.text(data.exists ? 'IP Address sudah didaftarkan.' : '');
        });
      }, 500);
    } else {
      $ipError.text('');
    }
  });

  // Validasi saat submit
  const confirmColor = document.querySelector('#confirm-color');
  if (confirmColor) {
    confirmColor.onclick = async function (e) {
      const form = confirmColor.closest('form');

      if (!form.checkValidity()) {
        e.preventDefault();

        await Swal.fire({
          title: 'Form Belum Lengkap',
          text: 'Silakan isi semua field yang wajib diisi.',
          icon: 'warning',
          customClass: {
            confirmButton: 'btn btn-warning'
          }
        });

        return;
      }

      e.preventDefault();

      Swal.fire({
        title: 'Apakah data sudah benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batalkan',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
              form.submit();
            }
          });
        } else {
          Swal.fire({
            icon: 'info',
            title: 'Pendaftaran server dibatalkan.',
            showConfirmButton: false,
            timer: 1500,
            customClass: {
              popup: 'colored-toast'
            },
            toast: true,
            position: 'top-end'
          });
        }
      });
    };
  }

  // Konfirmasi reset
  const resetButton = document.querySelector('button[type="reset"]');
  if (resetButton) {
    resetButton.addEventListener('click', function (e) {
      e.preventDefault();
      const form = resetButton.closest('form');

      Swal.fire({
        title: 'Batalkan Form?',
        text: 'Seluruh data yang telah kamu isi akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, kosongkan',
        cancelButtonText: 'Tidak',
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          form.reset();

          $('.select2').val('').trigger('change');
          $('#charCount').text('500 karakter tersisa');

          Swal.fire({
            icon: 'info',
            title: 'Form dikosongkan',
            showConfirmButton: false,
            timer: 1200,
            toast: true,
            position: 'top-end',
            customClass: {
              popup: 'colored-toast'
            }
          });
        }
      });
    });
  }

  const fieldsToTrim = ['#multicol-username', '#multicol-ipaddr', '#multicol-hostname'];

  fieldsToTrim.forEach(selector => {
    $(document).on('input blur', selector, function () {
      this.value = this.value.replace(/^\s+|\s+$/g, '');
    });
  });

  // Validasi akhir submit (HTML5 dan error text)
  $form.on('submit', function (e) {
    // Trim semua field penting sekali lagi
    fieldsToTrim.forEach(selector => {
      const $field = $(selector);
      $field.val($field.val().trim());
    });

    // Validasi custom
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
