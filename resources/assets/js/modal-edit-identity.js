/**
 * Edit Identity Modal
 */
'use strict';

$(function () {
  // Inisialisasi Select2
  $('.select2').each(function () {
    const $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih nilai',
      dropdownParent: $this.parent()
    });
  });

  // Tombol simpan perubahan
  $('#btnUpdateIdentity').on('click', function () {
    Swal.fire({
      title: 'Simpan Perubahan?',
      text: 'Perubahan ini akan memperbarui data identity.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batalkan',
      focusConfirm: true,
      returnFocus: false,
      preConfirm: () => {
        const $btn = $('#btnUpdateIdentity');
        $btn.prop('disabled', true).text('Menyimpan...');

        const id = $('#editIdentityId').val();
        const formData = {
          _method: 'PUT',
          _token: $('meta[name="csrf-token"]').attr('content'),
          hostname: $('#editHostname').val(),
          ip_addr_srv: $('#editIpAddress').val(),
          platform_id: $('#editPlatform').val(),
          username: $('#editUsername').val(),
          functionality: $('#editFunctionality').val(),
          description: $('#editDescription').val()
        };

        return new Promise((resolve, reject) => {
          $.ajax({
            url: `/identity/${id}`,
            method: 'POST',
            data: formData,
            success: function (res) {
              resolve({ res, formData });
            },
            error: function (err) {
              reject(new Error(err.responseJSON?.message || 'Terjadi kesalahan saat memperbarui data.'));
            },
            complete: function () {
              $btn.prop('disabled', false).text('Simpan Perubahan');
            }
          });
        });
      }
    })
      .then(result => {
        if (!result.isConfirmed || !result.value) return;

        const { res, formData } = result.value;

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        });

        // Tutup modal & reset form
        const modalEl = document.getElementById('editIdentity');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.hide();

        $('#editIdentityForm')[0].reset();
        $('.select2').val(null).trigger('change');
        $('.datatables-users').DataTable().ajax.reload(null, false);

        // Perbarui tampilan
        $('#text-hostname').text(formData.hostname);
        $('#text-ip').text(formData.ip_addr_srv);
        $('#text-username').text(formData.username);
        $('#text-functionality').text(formData.functionality);
        $('#text-platform').text(window.platformList[formData.platform_id] || '-');
        $('#text-updated-by').text(res.updated_by_name || '-');
        $('#text-updated-at').text(res.updated_at || '-');

        // Update data tombol edit
        $('.btn-edit-identity').data({
          hostname: formData.hostname,
          ip_addr_srv: formData.ip_addr_srv,
          username: formData.username,
          functionality: formData.functionality,
          platform_id: formData.platform_id,
          description: formData.description
        });
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: err.message
        });
      });
  });

  // Isi data saat klik tombol edit
  $(document).on('click', '.btn-edit-identity', function () {
    const data = $(this).data();
    $('#editIdentityId').val(data.id);
    $('#editHostname').val(data.hostname);
    $('#editIpAddress').val(data.ip_addr_srv);
    $('#editPlatform').val(data.platform_id).trigger('change');
    $('#editUsername').val(data.username);
    $('#editFunctionality').val(data.functionality).trigger('change');
    $('#editDescription').val(data.description);

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editIdentity'));
    modal.show();
  });

  // Reset form saat modal ditutup
  document.getElementById('editIdentity').addEventListener('hidden.bs.modal', function () {
    $('#editIdentityForm')[0].reset();
    $('.select2').val(null).trigger('change');
  });
});

// Mencegah Enter menutup modal saat SweetAlert terbuka,
// namun tetap bisa trigger confirm SweetAlert
document.addEventListener('keydown', function (e) {
  if (e.key === 'Enter' && Swal.isVisible()) {
    const activeElement = document.activeElement;
    const isInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement?.tagName);
    if (!isInput) {
      e.preventDefault();
      document.querySelector('.swal2-confirm')?.click(); // trigger simpan
    }
  }
});
