/**
 * Edit Identity Modal
 */
'use strict';

// Inisialisasi Select2
$(function () {
  const select2 = $('.select2');

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Pilih nilai',
        dropdownParent: $this.parent()
      });
    });
  }

  // Submit form update identity
  $('#editIdentityForm').on('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Simpan Perubahan?',
      text: 'Perubahan ini akan memperbarui data identity.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batalkan'
    }).then(result => {
      if (result.isConfirmed) {
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

        $.ajax({
          url: `/identity/${id}`,
          method: 'POST',
          data: formData,
          success: function (res) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: res.message,
              timer: 1500,
              showConfirmButton: false
            });

            // Tutup modal & reset form
            $('#editIdentity').modal('hide');
            $('#editIdentityForm')[0].reset();
            $('.select2').val(null).trigger('change');

            // 🔁 Update isi halaman langsung
            $('#text-hostname').text(formData.hostname);
            $('#text-ip').text(formData.ip_addr_srv);
            $('#text-username').text(formData.username);
            $('#text-functionality').text(formData.functionality);
            $('#text-platform').text(window.platformList[formData.platform_id] || '-');
            $('#text-updated-by').text(res.updated_by_name || '-');
            $('#text-updated-at').text(res.updated_at || '-');

            // 🔄 Update atribut data tombol edit
            const $editBtn = $('.btn-edit-identity');
            $editBtn.data('hostname', formData.hostname);
            $editBtn.data('ip_addr_srv', formData.ip_addr_srv);
            $editBtn.data('username', formData.username);
            $editBtn.data('functionality', formData.functionality);
            $editBtn.data('platform_id', formData.platform_id);
            $editBtn.data('description', formData.description);
          },
          error: function (err) {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: err.responseJSON?.message || 'Terjadi kesalahan saat memperbarui data.'
            });
          },
          complete: function () {
            $btn.prop('disabled', false).text('Simpan Perubahan');
          }
        });
      }
    });
  });

  $(document).on('click', '.btn-edit-identity', function () {
    const data = $(this).data();
    $('#editIdentityId').val(data.id);
    $('#editHostname').val(data.hostname);
    $('#editIpAddress').val(data.ip_addr_srv);
    $('#editPlatform').val(data.platform_id).trigger('change');
    $('#editUsername').val(data.username);
    $('#editFunctionality').val(data.functionality).trigger('change');
    $('#editDescription').val(data.description);
    $('#editIdentity').modal('show');
  });

  // Reset form saat modal ditutup
  $('#editIdentity').on('hidden.bs.modal', function () {
    $('#editIdentityForm')[0].reset();
    $('.select2').val(null).trigger('change');
  });
});
