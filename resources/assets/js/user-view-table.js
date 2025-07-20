'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('.datatable-activity');
  if (!table) return;

  const userId = table.dataset.userId;

  const dtActivity = new DataTable(table, {
    ajax: `/user/${userId}/activity-log`,
    processing: true,
    serverSide: true,
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'note', name: 'note' }, // diganti dari 'event_type'
      { data: 'event_time', name: 'event_time' },
      { data: 'user', name: 'user' },
      { data: 'actor_ip_addr', name: 'actor_ip_addr' }
    ],
    order: [[2, 'desc']],
    columnDefs: [
      {
        targets: 0,
        orderable: false,
        searchable: false,
        className: 'text-center'
      },
      {
        targets: 1,
        render: function (data) {
          if (!data) return '-';
          return `<span class="text-wrap">${data}</span>`;
        }
      }
    ],
    language: {
      processing: `
        <div class="d-flex justify-content-center align-items-center py-5">
          <div class="sk-fold">
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
            <div class="sk-fold-cube"></div>
          </div>
        </div>
      `,
      search: 'Cari:',
      lengthMenu: 'Tampilkan _MENU_ data',
      info: 'Menampilkan _START_ - _END_ dari _TOTAL_ log',
      paginate: {
        next: '<i class="bx bx-chevron-right"></i>',
        previous: '<i class="bx bx-chevron-left"></i>'
      }
    },
    responsive: true
  });
});
