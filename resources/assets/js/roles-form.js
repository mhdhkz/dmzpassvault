$(function () {
  // Inisialisasi Select2
  $('.select2').each(function () {
    const $this = $(this);
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih opsi',
      dropdownParent: $this.parent()
    });
  });

  // Tombol Submit Form Tambah Role
  const submitBtn = document.querySelector('#btn-submit-role');
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
        title: 'Simpan Role Position?',
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
        }
      });
    };
  }

  // Konfirmasi Reset Form Tambah
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

  // Konfirmasi Hapus Role
  $(document).on('submit', '.delete-form', function (e) {
    e.preventDefault();
    const form = this;

    Swal.fire({
      title: 'Yakin ingin menghapus role ini?',
      text: 'Data yang dihapus tidak bisa dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(result => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  });

  // Event tombol Edit → buka modal
  $(document).on('click', '.btn-edit-role', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const platformStr = $(this).data('platforms');
    const platformIds = platformStr ? platformStr.toString().split(',') : [];

    $('#edit_id').val(id);
    $('#edit_position_name').val(name);
    $('#edit_platform_ids').val(platformIds).trigger('change');

    const action = `/admin/role-form/${id}/update`;
    $('#form-edit-role').attr('action', action);
    $('#modal-edit-role').modal('show');
  });

  // Submit Form Edit (Modal) dengan Loading
  $('#form-edit-role').on('submit', function (e) {
    e.preventDefault();
    const form = this;

    if (!form.checkValidity()) {
      Swal.fire({
        title: 'Form Belum Lengkap',
        text: 'Silakan isi semua field yang wajib diisi.',
        icon: 'warning',
        customClass: { confirmButton: 'btn btn-warning' }
      });
      return;
    }

    Swal.fire({
      title: 'Simpan Perubahan?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batalkan',
      customClass: {
        confirmButton: 'btn btn-warning',
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
      }
    });
  });
});
