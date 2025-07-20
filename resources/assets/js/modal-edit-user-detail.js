'use strict';

$(function () {
  const $modal = $('#editUserDetailModal');
  const $form = $('#editUserDetailForm');
  const $btn = $('#btnUpdateUserDetail');

  // =====================[ 1. Inisialisasi Select2 ]==========================
  $('.select2').each(function () {
    const $this = $(this);
    if (!$this.hasClass('select2-hidden-accessible')) {
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Pilih Posisi',
        dropdownParent: $this.parent()
      });
    }
  });

  // ================[ 2. Isi form saat tombol edit diklik ]===================
  $('.btn-edit-user-detail').on('click', function () {
    const $el = $(this);

    // Reset terlebih dahulu
    $form[0].reset();
    $('.select2').val(null).trigger('change');

    // Delay agar render sempurna sebelum isi
    setTimeout(() => {
      $('#editUserDetailId').val($el.data('id'));
      $('#editUserDetailName').val($el.data('name'));
      $('#editUserDetailEmail').val($el.data('email'));
      $('#editUserDetailRole').val($el.data('role')).trigger('change');
      $('#editUserDetailBirthDate').val($el.data('birth_date'));
      $('#editUserDetailNationality').val($el.data('nationality'));
      $('#editUserDetailEmployeeId').val($el.data('employee_id'));
      $('#editUserDetailJobTitle').val($el.data('job_title'));
      const $positionInput = $('#editUserDetailPosition');
      if ($positionInput.is('select')) {
        $positionInput.val($el.data('position_id')).trigger('change');
      } else {
        $positionInput.val($el.data('position_id'));
      }

      $('#editUserDetailWorkMode').val($el.data('work_mode')).trigger('change');
      $('#editUserDetailWorkLocation').val($el.data('work_location'));
    }, 100);
  });

  // =================[ 3. Tombol simpan perubahan ]===========================
  $btn.on('click', function () {
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
        const id = $('#editUserDetailId').val();
        if (!id) return Swal.showValidationMessage('ID user tidak ditemukan.');

        $btn.prop('disabled', true).text('Menyimpan...');

        const formData = {
          _method: 'PUT',
          _token: $('meta[name="csrf-token"]').attr('content'),
          name: $('#editUserDetailName').val(),
          email: $('#editUserDetailEmail').val(),
          role: $('#editUserDetailRole').val(),
          birth_date: $('#editUserDetailBirthDate').val(),
          nationality: $('#editUserDetailNationality').val(),
          employee_id: $('#editUserDetailEmployeeId').val(),
          job_title: $('#editUserDetailJobTitle').val(),
          position_id: $('#editUserDetailPosition').val(),
          work_mode: $('#editUserDetailWorkMode').val(),
          work_location: $('#editUserDetailWorkLocation').val()
        };

        return new Promise((resolve, reject) => {
          $.ajax({
            url: `/admin/user-list/${id}`,
            method: 'POST',
            data: formData,
            success: function (res) {
              res.success ? resolve(res) : reject(new Error(res.message || 'Gagal menyimpan'));
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

        const res = result.value;

        // Tutup modal
        const modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
        modalInstance.hide();
        $form[0].reset();
        $('.select2').val(null).trigger('change');

        // Handle kondisi logout / redirect
        if (res.email_changed && res.is_self) {
          return Swal.fire({
            icon: 'success',
            title: 'Email berhasil diubah',
            text: 'Kamu akan logout dan diminta login ulang.',
            confirmButtonText: 'OK'
          }).then(() => {
            $('<form>', {
              method: 'POST',
              action: '/logout'
            })
              .append(
                $('<input>', {
                  type: 'hidden',
                  name: '_token',
                  value: $('meta[name="csrf-token"]').attr('content')
                })
              )
              .appendTo('body')
              .submit();
          });
        }

        if (res.role_changed && res.is_self) {
          return Swal.fire({
            icon: 'info',
            title: 'Perubahan Role',
            text: 'Role kamu telah diubah. Akan diarahkan ke dashboard.',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            window.location.href = '/dashboard';
          });
        }

        // Sukses biasa
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: res.message,
          timer: 1500,
          showConfirmButton: false
        });

        if (window.location.pathname.includes('/user/detail')) {
          window.location.reload();
        }

        const dt = $('.datatables-userlist').DataTable();
        if (dt) dt.ajax.reload(null, false);
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: err.message
        });
      });
  });

  // ==================[ 4. Reset modal saat ditutup ]=========================
  $modal.on('hidden.bs.modal', function () {
    $form[0].reset();
    $('.select2').val(null).trigger('change');
  });

  // ==========[ 5. Force close Select2 jika terbuka lalu klik input lain ]====
  $(document).on('mousedown', 'input, textarea, select:not(.select2-hidden-accessible)', function () {
    if ($('.select2-container--open').length) {
      $('select.select2').select2('close');
    }
  });

  // ===========[ 6. Fix: Tekan Enter di swal langsung trigger confirm ]========
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
