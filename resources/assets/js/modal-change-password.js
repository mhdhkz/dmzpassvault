'use strict';

$(function () {
  // Saat tombol ganti password diklik
  $('.btn-password-user').on('click', function () {
    const userId = $(this).data('id');
    const userName = $(this).data('name');

    $('#changePasswordUserId').val(userId);
    $('#changePasswordUsername').text(userName);
    $('#changePasswordForm')[0].reset();
  });

  // Submit form ganti password
  $('#changePasswordForm').on('submit', function (e) {
    e.preventDefault();

    const userId = $('#changePasswordUserId').val();
    const password = $('#newPassword').val();
    const confirm = $('#confirmPassword').val();

    if (password !== confirm) {
      return Swal.fire('Error', 'Konfirmasi password tidak cocok.', 'error');
    }

    Swal.fire({
      title: 'Ganti Password?',
      text: 'Password baru akan langsung diterapkan.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Ganti',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (!result.isConfirmed) return;

      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).text('Mengganti...');

      $.ajax({
        url: `/admin/user-list/${userId}/change-password`,
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          password: password,
          password_confirmation: confirm
        },
        success: function (res) {
          const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('changePassword'));
          modal.hide();
          $('#changePasswordForm')[0].reset();

          // ✅ Logout jika user sedang mengganti password dirinya sendiri
          if (res.is_self) {
            return Swal.fire({
              icon: 'success',
              title: 'Password berhasil diubah',
              text: 'Kamu akan logout dan diminta login ulang.',
              confirmButtonText: 'OK'
            }).then(() => {
              const logoutForm = document.createElement('form');
              logoutForm.method = 'POST';
              logoutForm.action = '/logout';
              const token = document.querySelector('meta[name="csrf-token"]').content;
              const csrfInput = document.createElement('input');
              csrfInput.type = 'hidden';
              csrfInput.name = '_token';
              csrfInput.value = token;
              logoutForm.appendChild(csrfInput);
              document.body.appendChild(logoutForm);
              logoutForm.submit();
            });
          }

          // ✅ Jika bukan dirinya sendiri
          Swal.fire('Berhasil', res.message || 'Password berhasil diubah.', 'success');
        },
        error: function (err) {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: err.responseJSON?.message || 'Terjadi kesalahan saat mengganti password.'
          });
        },
        complete: function () {
          $btn.prop('disabled', false).text('Ganti Password');
        }
      });
    });
  });

  // Reset form saat modal ditutup
  document.getElementById('changePassword').addEventListener('hidden.bs.modal', function () {
    $('#changePasswordForm')[0].reset();
  });
});
