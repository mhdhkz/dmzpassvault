'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // 🔴 DELETE VAULT REQUEST
  $(document).on('click', '.btn-delete-request', function () {
    const id = $(this).data('id');

    Swal.fire({
      title: 'Hapus Request?',
      text: 'Permintaan vault ini akan dihapus permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/vault/${id}`,
          type: 'DELETE',
          data: {
            _token: csrfToken
          },
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            });

            setTimeout(() => {
              window.location.href = '/vault/vault-list';
            }, 1600);
          },
          error: function (err) {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: err.responseJSON?.message || 'Terjadi kesalahan saat menghapus.'
            });
          }
        });
      }
    });
  });

  // 🔵 DELETE IDENTITY (HANYA JIKA TOMBOL TERSEDIA)
  const deleteIdentityBtn = document.querySelector('.delete-identity');
  if (deleteIdentityBtn) {
    deleteIdentityBtn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');

      Swal.fire({
        title: 'Apakah kamu yakin?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/identity/delete/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              Accept: 'application/json'
            }
          })
            .then(res => {
              if (!res.ok) throw new Error('Gagal menghapus');
              return res.json();
            })
            .then(data => {
              if (data.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Data telah dihapus.',
                  showConfirmButton: false,
                  timer: 1500
                }).then(() => {
                  window.location.href = '/identity/identity-list';
                });
              } else {
                throw new Error(data.message || 'Gagal menghapus');
              }
            })
            .catch(err => {
              Swal.fire('Error', err.message, 'error');
            });
        }
      });
    });
  }
});
