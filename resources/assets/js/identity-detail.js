'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const deleteBtn = document.querySelector('.delete-identity');

  if (deleteBtn) {
    deleteBtn.addEventListener('click', function () {
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
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                  // Redirect ke halaman list
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
