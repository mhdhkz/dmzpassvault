$(function () {
  // Inisialisasi Select2
  $('.select2').each(function () {
    const $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih opsi',
      dropdownParent: $this.parent()
    });
  });

  // Tombol Submit
  const submitBtn = document.querySelector('#btn-submit-user');
  if (submitBtn) {
    submitBtn.onclick = async function (e) {
      const form = submitBtn.closest('form');
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
        title: 'Simpan User Baru?',
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

              // Trim hanya password
              const $password = $('#password');
              const $confirm = $('#password_confirmation');
              $password.val($password.val().trim());
              $confirm.val($confirm.val().trim());

              form.submit();
            }
          });
        }
      });
    };
  }

  // Konfirmasi Reset
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
});
