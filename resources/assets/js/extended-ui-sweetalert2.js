/**
 * Sweet Alerts (with Reset Confirmation)
 */

'use strict';

(function () {
  const confirmColor = document.querySelector('#confirm-color');
  const resetButton = document.querySelector('button[type="reset"]');

  // Validasi dan konfirmasi simpan
  if (confirmColor) {
    confirmColor.onclick = async function (e) {
      const form = confirmColor.closest('form');

      // Cek validasi HTML5
      if (!form.checkValidity()) {
        e.preventDefault();

        await Swal.fire({
          title: 'Form Belum Lengkap',
          text: 'Silakan isi semua field yang wajib diisi.',
          icon: 'warning',
          customClass: {
            confirmButton: 'btn btn-warning'
          },
          focusConfirm: true,
          allowEnterKey: true
        });

        return;
      }

      // Mencegah submit default
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
      }).then(function (result) {
        if (result.isConfirmed) {
          form.submit();
        } else {
          Swal.fire({
            title: 'Dibatalkan',
            text: 'Pendaftaran server dibatalkan.',
            icon: 'info',
            customClass: {
              confirmButton: 'btn btn-success'
            }
          });
        }
      });
    };
  }

  // Konfirmasi sebelum reset
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

          // Reset select2
          const selects = form.querySelectorAll('.select2');
          selects.forEach(select => {
            if ($(select).hasClass('select2-hidden-accessible')) {
              $(select).val('').trigger('change');
            }
          });

          // Reset counter karakter textarea (jika ada)
          const charCount = document.getElementById('charCount');
          if (charCount) {
            charCount.textContent = '500 karakter tersisa';
          }

          Swal.fire({
            icon: 'info',
            title: 'Form dikosongkan',
            showConfirmButton: false,
            timer: 1200,
            customClass: {
              popup: 'mt-3',
              title: 'text-sm'
            }
          });
        }
      });
    });
  }
})();
