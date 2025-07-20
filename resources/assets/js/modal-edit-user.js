'use strict';

$(function () {
  const $editModal = $('#editUserList');
  const $editForm = $('#editUserListForm');
  const $roleSelect = $('#editUserRole');

  // 🧼 Inisialisasi awal Select2
  $('.select2').each(function () {
    const $this = $(this);
    if (!$this.hasClass('select2-hidden-accessible')) {
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Pilih nilai',
        dropdownParent: $this.parent()
      });
    }
  });

  // ✅ Inisialisasi ulang Select2 saat modal dibuka
  $editModal.on('shown.bs.modal', function () {
    if ($roleSelect.hasClass('select2-hidden-accessible')) {
      $roleSelect.select2('destroy');
    }

    $roleSelect.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih nilai',
      dropdownParent: $roleSelect.parent()
    });
  });

  // 🧹 Reset form saat modal ditutup
  $editModal.on('hidden.bs.modal', function () {
    $editForm[0].reset();
    $('.select2').val(null).trigger('change');
  });

  // 🔘 Tombol Simpan Perubahan
  $('#btnUpdateUserList').on('click', function () {
    Swal.fire({
      title: 'Simpan Perubahan?',
      text: 'Perubahan ini akan memperbarui data user.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batalkan',
      focusConfirm: true,
      returnFocus: false,
      preConfirm: () => {
        const $btn = $('#btnUpdateUserList');
        $btn.prop('disabled', true).text('Menyimpan...');

        const id = $('#editUserId').val();
        const formData = {
          _method: 'PUT',
          _token: $('meta[name="csrf-token"]').attr('content'),
          name: $('#editUserName').val(),
          email: $('#editUserEmail').val(),
          role: $roleSelect.val()
        };

        return new Promise((resolve, reject) => {
          $.ajax({
            url: `/admin/user-list/${id}`,
            method: 'POST',
            data: formData,
            success: res => {
              res.success ? resolve(res) : reject(new Error(res.message || 'Gagal memperbarui data.'));
            },
            error: err => {
              reject(new Error(err.responseJSON?.message || 'Terjadi kesalahan saat memperbarui data.'));
            },
            complete: () => {
              $btn.prop('disabled', false).text('Simpan Perubahan');
            }
          });
        });
      }
    })
      .then(result => {
        if (!result.isConfirmed || !result.value) return;

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: result.value.message,
          timer: 1500,
          showConfirmButton: false
        });

        const modalInstance = bootstrap.Modal.getOrCreateInstance($editModal[0]);
        modalInstance.hide();
        $editForm[0].reset();
        $('.select2').val(null).trigger('change');
        $('.datatables-userlist').DataTable().ajax.reload(null, false);
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: err.message
        });
      });
  });

  // ⛔️ Fix: Tutup Select2 jika user klik input lain saat dropdown terbuka
  $(document).on('mousedown', 'input, textarea, select:not(.select2-hidden-accessible)', function () {
    if ($('.select2-container--open').length) {
      $('select.select2').select2('close');
    }
  });

  // ⏎ Enter di Swal langsung trigger simpan
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && Swal.isVisible()) {
      const activeElement = document.activeElement;
      const isInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement?.tagName);
      if (!isInput) {
        e.preventDefault();
        document.querySelector('.swal2-confirm')?.click();
      }
    }
  });
});
