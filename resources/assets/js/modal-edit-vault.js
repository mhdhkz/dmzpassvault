'use strict';

$(function () {
  // Inisialisasi Select2
  const select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      $(this)
        .wrap('<div class="position-relative"></div>')
        .select2({
          placeholder: 'Pilih nilai',
          dropdownParent: $(this).parent()
        });
    });
  }

  // 🔁 Fetch data saat tombol edit diklik
  $(document).on('click', '.btn-edit-request', function () {
    const id = $(this).data('id');

    $.get(`/vault/${id}/json`, function (data) {
      const start = moment(data.start_at).format('YYYY-MM-DD HH:mm');
      const end = moment(data.end_at).format('YYYY-MM-DD HH:mm');

      $('#editRequestId').val(data.id);
      $('#editRequestIdentifier').val(data.request_id);
      $('#editPurpose').val(data.purpose);
      $('#editDurationRange').val(`${start} - ${end}`).data('start', start).data('end', end);

      $('#editRequestModal').modal('show');
    });
  });

  // Daterangepicker
  $('#editDurationRange').daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    autoUpdateInput: false,
    locale: {
      format: 'YYYY-MM-DD HH:mm',
      applyLabel: 'Pilih',
      cancelLabel: 'Batal'
    },
    maxSpan: { days: 5 },
    parentEl: '#editRequestModal',
    appendTo: '#editRequestModal'
  });

  $('#editDurationRange').on('apply.daterangepicker', function (ev, picker) {
    const start = picker.startDate.format('YYYY-MM-DD HH:mm');
    const end = picker.endDate.format('YYYY-MM-DD HH:mm');
    $(this).val(`${start} - ${end}`).data('start', start).data('end', end);
  });

  // Reset modal
  $('#editRequestModal').on('hidden.bs.modal', function () {
    $('#editRequestForm')[0].reset();
    $('#editDurationRange').val('').removeData('start').removeData('end');
  });

  // Submit update
  $('#editRequestForm').on('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Simpan Perubahan?',
      text: 'Perubahan ini akan memperbarui data permintaan vault.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        const $btn = $('#btnUpdateRequest');
        $btn.prop('disabled', true).text('Menyimpan...');

        const id = $('#editRequestId').val();
        const formData = {
          _method: 'PUT',
          _token: $('meta[name="csrf-token"]').attr('content'),
          purpose: $('#editPurpose').val(),
          start_time: $('#editDurationRange').data('start'),
          end_time: $('#editDurationRange').data('end'),
          duration_minutes: moment($('#editDurationRange').data('end')).diff(
            moment($('#editDurationRange').data('start')),
            'minutes'
          ),
          duration_range: $('#editDurationRange').val()
        };

        $.ajax({
          url: `/vault/${id}`,
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

            $('#text-purpose').text(formData.purpose);
            $('#text-duration').text(formData.duration_minutes + ' mins');

            $('#editRequestModal').modal('hide');
            $('#editRequestForm')[0].reset();
            $('.datatables-users').DataTable().ajax.reload(null, false);
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
});
