/**
 * Edit Vault Modal
 */
'use strict';

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

  let startDate, endDate;

  // 🟡 FILL: Modal Edit Vault
  $(document).on('click', '.btn-edit-request', function () {
    const data = $(this).data();

    $('#editRequestId').val(data.id);
    $('#editRequestIdentifier').val(data.request_id);
    $('#editPurpose').val(data.purpose);

    if (data.start_time && data.end_time) {
      $('#editDurationRange')
        .val(`${data.start_time} - ${data.end_time}`)
        .data('start', data.start_time)
        .data('end', data.end_time);
    }

    $('#editRequestModal').modal('show');
  });

  // 🟡 INISIALISASI: DateRangePicker saat modal ditampilkan
  $('#editDurationRange').daterangepicker({
    timePicker: true,
    timePicker24Hour: true,
    autoUpdateInput: false,
    locale: {
      format: 'YYYY-MM-DD HH:mm',
      cancelLabel: 'Batal',
      applyLabel: 'Pilih'
    },
    maxSpan: { days: 5 },
    parentEl: '#editRequestModal', // biar tetap di dalam modal
    appendTo: '#editRequestModal' // arahkan rendering DOM ke modal
  });

  $('#editDurationRange').on('apply.daterangepicker', function (ev, picker) {
    const value = `${picker.startDate.format('YYYY-MM-DD HH:mm')} - ${picker.endDate.format('YYYY-MM-DD HH:mm')}`;
    $(this)
      .val(value) // ⬅️ inilah yang bikin input terisi
      .data('start', picker.startDate.format('YYYY-MM-DD HH:mm'))
      .data('end', picker.endDate.format('YYYY-MM-DD HH:mm'));
  });

  // 🟡 RESET saat modal ditutup
  $('#editRequestModal').on('hidden.bs.modal', function () {
    $('#editRequestForm')[0].reset();
    $('#editDurationRange').val('').removeData('start').removeData('end');
  });

  // 🟡 SUBMIT: Vault Update
  $('#editRequestForm').on('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Simpan Perubahan?',
      text: 'Perubahan ini akan memperbarui data permintaan vault.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batalkan'
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
          )
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
            $('#text-duration').text(formData.duration_minutes + ' menit');

            $('#editRequestModal').modal('hide');
            $('#editRequestForm')[0].reset();
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
