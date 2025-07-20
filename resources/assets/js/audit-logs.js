/**
 * Page Audit Logs
 */

'use strict';
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: toast => {
    toast.classList.add('colored-toast');
  }
});

// Datatable (js)
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  // Variable declaration for table
  const dt_user_table = document.querySelector('.datatables-audit');
  var select2 = $('.select2');

  // Inisialisasi toast swal

  const showSuccessToast = msg => {
    Toast.fire({
      icon: 'success',
      title: msg
    });
  };

  const showErrorToast = msg => {
    Toast.fire({
      icon: 'error',
      title: msg
    });
  };

  if (select2.length) {
    var $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih Event Type',
      dropdownParent: $this.parent()
    });
  }

  // Users datatable
  let dt_user;
  if (dt_user_table) {
    dt_user = new DataTable(dt_user_table, {
      ajax: {
        url: '/admin/audit-logs/data',
        data: function (d) {
          d.event_type = $('#filterEventType').val();
          d.user_name = $('#filterUserName').val();
          d.actor_ip = $('#filterActorIp').val();
        }
      },
      processing: true,
      serverSide: true,
      orderMulti: false,
      order: [],
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null },
        { data: 'hostname' },
        { data: 'ip_address' },
        { data: 'event_type' },
        { data: 'event_time' },
        { data: 'user_name' },
        { data: 'actor_ip_addr' }
      ],
      columnDefs: [
        {
          // For Responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // For Checkboxes
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          checkboxes: true,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 2, // indeks kolom nomor urut
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            return meta.row + 1 + meta.settings._iDisplayStart;
          }
        },
        {
          targets: 3,
          responsivePriority: 3,
          render: function (data, type, full, meta) {
            var hostname = full['hostname'];
            var initials = (hostname.match(/\b\w/g) || []).map(char => char.toUpperCase());
            initials = ((initials.shift() || '') + (initials.pop() || '')).toUpperCase();
            var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
            var state = states[Math.floor(Math.random() * states.length)];
            var avatar = `<span class="avatar-initial rounded-circle bg-label-${state}">${initials}</span>`;

            return `
              <div class="d-flex justify-content-start align-items-center user-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-4">${avatar}</div>
                </div>
                <div class="d-flex flex-column">
                  <span class="fw-medium text-heading">${hostname}</span>
                </div>
              </div>
            `;
          }
        },
        {
          targets: 4,
          render: function (data, type, full) {
            return `<span class="text-heading">${full['ip_address']}</span>`;
          }
        },
        {
          targets: 5,
          render: function (data, type, full, meta) {
            var event_type = full['event_type'];
            return '<span class="text-heading">' + event_type + '</span>';
          }
        },
        {
          targets: 6,
          render: function (data, type, full, meta) {
            var event_time = full['event_time'];
            if (event_time) {
              try {
                const dateLocal = new Date(event_time); // Langsung parse
                const yyyy = dateLocal.getFullYear();
                const mm = String(dateLocal.getMonth() + 1).padStart(2, '0');
                const dd = String(dateLocal.getDate()).padStart(2, '0');
                const hh = String(dateLocal.getHours()).padStart(2, '0');
                const min = String(dateLocal.getMinutes()).padStart(2, '0');

                return `<span class="text-heading">${yyyy}-${mm}-${dd} ${hh}:${min}</span>`;
              } catch (e) {
                console.error('Invalid date format:', event_time, e);
                return `<span class="text-heading">${event_time}</span>`;
              }
            } else {
              return '-';
            }
          }
        },
        {
          targets: 7,
          render: function (data, type, full) {
            return `<span class="text-heading">${full['user_name']}</span>`;
          }
        },
        {
          targets: 8,
          render: function (data, type, full) {
            return `<span class="text-heading">${full['actor_ip_addr']}</span>`;
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      layout: {
        topStart: {
          rowClass: 'row mx-3 my-0 justify-content-between',
          features: [
            {
              pageLength: {
                menu: [10, 25, 50, 100],
                text: '_MENU_'
              }
            }
          ]
        },
        topEnd: {
          features: [
            {
              search: {
                placeholder: 'Cari Identity',
                text: '_INPUT_'
              }
            },
            {
              buttons: [
                {
                  extend: 'collection',
                  className: 'btn btn-label-secondary dropdown-toggle',
                  text: '<span class="d-flex align-items-center gap-2"><i class="icon-base bx bx-export icon-sm"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                  buttons: [
                    {
                      extend: 'print',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-printer me-2"></i>Print</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7, 8],
                        format: {
                          body: function (inner) {
                            if (!inner) return '';

                            const parsed = new DOMParser().parseFromString(inner, 'text/html');
                            const container = parsed.body;

                            // Coba cari langsung elemen dengan class .user-name .text-heading
                            const textEl = container.querySelector('.user-name .text-heading');

                            if (textEl) return textEl.textContent.trim();

                            // Jika tidak ditemukan, fallback ke textContent seluruhnya
                            return container.textContent.trim();
                          }
                        }
                      },
                      customize: function (win) {
                        win.document.body.style.color = config.colors.headingColor;
                        win.document.body.style.borderColor = config.colors.borderColor;
                        win.document.body.style.backgroundColor = config.colors.bodyBg;
                        const table = win.document.body.querySelector('table');
                        table.classList.add('compact');
                        table.style.color = 'inherit';
                        table.style.borderColor = 'inherit';
                        table.style.backgroundColor = 'inherit';
                      }
                    },
                    {
                      extend: 'csv',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bx-file me-2"></i>CSV</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7, 8],
                        format: {
                          body: function (inner) {
                            if (!inner) return '';

                            const parsed = new DOMParser().parseFromString(inner, 'text/html');
                            const container = parsed.body;

                            // Coba cari langsung elemen dengan class .user-name .text-heading
                            const textEl = container.querySelector('.user-name .text-heading');

                            if (textEl) return textEl.textContent.trim();

                            // Jika tidak ditemukan, fallback ke textContent seluruhnya
                            return container.textContent.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'excel',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-export me-2"></i>Excel</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7, 8],
                        format: {
                          body: function (inner) {
                            if (!inner) return '';

                            const parsed = new DOMParser().parseFromString(inner, 'text/html');
                            const container = parsed.body;

                            // Coba cari langsung elemen dengan class .user-name .text-heading
                            const textEl = container.querySelector('.user-name .text-heading');

                            if (textEl) return textEl.textContent.trim();

                            // Jika tidak ditemukan, fallback ke textContent seluruhnya
                            return container.textContent.trim();
                          }
                        }
                      }
                    },
                    {
                      extend: 'pdf',
                      text: `<span class="d-flex align-items-center"><i class="icon-base bx bxs-file-pdf me-2"></i>PDF</span>`,
                      className: 'dropdown-item',
                      exportOptions: {
                        columns: [3, 4, 5, 6, 7, 8],
                        format: {
                          body: function (inner) {
                            if (!inner) return '';

                            const parsed = new DOMParser().parseFromString(inner, 'text/html');
                            const container = parsed.body;

                            // Coba cari langsung elemen dengan class .user-name .text-heading
                            const textEl = container.querySelector('.user-name .text-heading');

                            if (textEl) return textEl.textContent.trim();

                            // Jika tidak ditemukan, fallback ke textContent seluruhnya
                            return container.textContent.trim();
                          }
                        }
                      }
                    }
                  ]
                }
              ]
            }
          ]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search User',
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
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base bx bx-chevron-left scaleX-n1-rtl icon-18px"></i>',
          first: '<i class="icon-base bx bx-chevrons-left scaleX-n1-rtl icon-18px"></i>',
          last: '<i class="icon-base bx bx-chevrons-right scaleX-n1-rtl icon-18px"></i>'
        }
      },
      // For responsive popup
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Details of ' + data['hostname'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== '' // Do not show row in modal popup if title is blank (for check box)
                  ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                      <td>${col.title}:</td>
                      <td>${col.data}</td>
                    </tr>`
                  : '';
              })
              .join('');

            if (data) {
              const div = document.createElement('div');
              div.classList.add('table-responsive');
              const table = document.createElement('table');
              div.appendChild(table);
              table.classList.add('table');
              const tbody = document.createElement('tbody');
              tbody.innerHTML = data;
              table.appendChild(tbody);
              return div;
            }
            return false;
          }
        }
      },
      // Replace initComplete function content with this
      initComplete: function () {
        const api = this.api();

        $('#filterEventType').on('change', function () {
          dt_user.draw();
        });
        $('#filterUserName').on('keyup change', function () {
          dt_user.draw();
        });
        $('#filterActorIp').on('keyup change', function () {
          dt_user.draw();
        });

        // Tombol Hapus Filter
        $('#clearFilterBtn').on('click', function () {
          $('#filterEventType').val('').trigger('change');
          $('#filterUserName').val('');
          $('#filterActorIp').val('');
          dt_user.draw();
        });
      }
    });
  }
  // Filter form control to default size
  // ? setTimeout used for user-list table initialization
  setTimeout(() => {
    const elementsToModify = [
      { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm', classToAdd: 'ms-0' },
      { selector: '.dt-length', classToAdd: 'mb-md-6 mb-0' },
      { selector: '.dt-search', classToAdd: 'mb-md-6 mb-2' },
      {
        selector: '.dt-layout-end',
        classToRemove: 'justify-content-between',
        classToAdd: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-4 flex-wrap mt-0'
      },
      { selector: '.dt-layout-start', classToAdd: 'mt-0' },
      { selector: '.dt-buttons', classToAdd: 'd-flex gap-4 mb-md-0 mb-6' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
    ];

    elementsToModify.forEach(item => {
      const el = document.querySelector(item.selector);
      if (!el) return;
      if (item.classToRemove) el.classList.remove(...item.classToRemove.split(' '));
      if (item.classToAdd) el.classList.add(...item.classToAdd.split(' '));
    });
  }, 100);
});
