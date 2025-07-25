$(function () {
  // Inisialisasi Select2
  $('.select2').each(function () {
    const $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih Identity',
      dropdownParent: $this.parent()
    });
  });

  // Inisialisasi Daterangepicker untuk durasi
  const $durationRange = $('#duration_range');
  if ($durationRange.length) {
    $durationRange.daterangepicker({
      timePicker: true,
      timePicker24Hour: true,
      locale: {
        format: 'YYYY-MM-DD HH:mm',
        separator: ' - ',
        applyLabel: 'Pilih',
        cancelLabel: 'Batal'
      },
      autoUpdateInput: true,
      minDate: moment(),
      maxSpan: { days: 4 }
    });
  }

  // Hitung karakter textarea tujuan
  const $textarea = $('#purpose');
  const $charCount = $(
    '<small id="purposeCharCount" class="form-text text-muted d-block mt-1">1200 karakter tersisa</small>'
  );
  if ($textarea.length) {
    $textarea.after($charCount);
    $textarea.on('input', function () {
      const max = 1200;
      const remaining = max - $(this).val().length;
      $charCount.text(`${remaining} karakter tersisa`);
      if (remaining < 0) {
        $(this).val($(this).val().substring(0, max));
        $charCount.text('0 karakter tersisa');
      }
    });
  }

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
          customClass: { confirmButton: 'btn btn-warning' }
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
            customClass: { popup: 'colored-toast' },
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
          $('#purposeCharCount').text('1200 karakter tersisa');

          Swal.fire({
            icon: 'info',
            title: 'Form dikosongkan',
            showConfirmButton: false,
            timer: 1200,
            toast: true,
            position: 'top-end',
            customClass: { popup: 'colored-toast' }
          });
        }
      });
    });
  }

  // Trim field penting
  const fieldsToTrim = ['#multicol-username', '#multicol-ipaddr', '#multicol-hostname'];
  fieldsToTrim.forEach(selector => {
    $(document).on('input blur', selector, function () {
      this.value = this.value.replace(/^\s+|\s+$/g, '');
    });
  });

  // Validasi akhir saat submit
  const $form = $('form');
  $form.on('submit', function () {
    fieldsToTrim.forEach(selector => {
      const $field = $(selector);
      $field.val($field.val().trim());
    });
  });

  // Tampilkan toast jika ada session sukses
  const toastMessage = $('meta[name="vault-success-message"]').attr('content');
  if (toastMessage) {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      customClass: { popup: 'colored-toast' },
      didOpen: toast => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    Toast.fire({
      icon: 'success',
      title: toastMessage
    });
  }

  // 🔵 Preview request ID
  const previewEl = document.getElementById('request-id-preview');
  if (previewEl) {
    fetch('/vault/next-request-id')
      .then(res => res.json())
      .then(data => {
        previewEl.textContent = data.next_id;

        // Tambahkan input hidden untuk dikirim ke backend (optional)
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'request_id_preview';
        hiddenInput.value = data.next_id;
        previewEl.closest('form').appendChild(hiddenInput);
      })
      .catch(() => {
        previewEl.innerHTML = '<span class="text-danger">Gagal memuat ID</span>';
      });
  }
});
