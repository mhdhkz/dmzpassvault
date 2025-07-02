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
          data: { _token: csrfToken },
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

  // 🟢 EDIT VAULT (AJAX load latest data)
  $(document).on('click', '.btn-edit-request', function () {
    const id = $(this).data('id');

    $.get(`/vault/${id}/json`, function (data) {
      const startTime = moment(data.start_at).format('YYYY-MM-DD HH:mm');
      const endTime = moment(data.end_at).format('YYYY-MM-DD HH:mm');

      $('#editRequestId').val(data.id);
      $('#editPurpose').val(data.purpose);
      $('#editDurationRange').val(`${startTime} - ${endTime}`).data('start', startTime).data('end', endTime);

      $('#editRequestModal').modal('show');
    });
  });

  // Approve
  $(document).on('click', '.btn-approve-request', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Approve Request?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Approve',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        $.post(`/vault/${id}/approve`, { _token: csrfToken }, function (res) {
          Swal.fire('Berhasil!', 'Request telah disetujui.', 'success').then(() => {
            location.reload();
          });
        });
      }
    });
  });

  // Reject
  $(document).on('click', '.btn-reject-request', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Tolak Request?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Tolak',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        $.post(`/vault/${id}/reject`, { _token: csrfToken }, function (res) {
          Swal.fire('Ditolak!', 'Request telah ditolak.', 'success').then(() => {
            location.reload();
          });
        });
      }
    });
  });
});
