/**
 * Page Vault Encrypt
 */

'use strict';

// Datatable (js)
document.addEventListener('DOMContentLoaded', function (e) {
  let borderColor, bodyBg, headingColor;

  borderColor = config.colors.borderColor;
  bodyBg = config.colors.bodyBg;
  headingColor = config.colors.headingColor;

  // Variable declaration for table
  const dt_user_table = document.querySelector('.datatables-users');
  var select2 = $('.select2');

  // Inisialisasi toast swal
  const showSuccessToast = msg => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      customClass: {
        popup: 'colored-toast'
      },
      didOpen: toast => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
    Toast.fire({ icon: 'success', title: msg });
  };

  const showErrorToast = msg => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      customClass: {
        popup: 'colored-toast'
      },
      didOpen: toast => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
    Toast.fire({ icon: 'error', title: msg });
  };

  if (select2.length) {
    var $this = select2;
    $this.wrap('<div class="position-relative"></div>').select2({
      placeholder: 'Pilih platform',
      dropdownParent: $this.parent()
    });
  }

  // Users datatable
  let dt_user;
  if (dt_user_table) {
    dt_user = new DataTable(dt_user_table, {
      ajax: '/identity/list/data',
      processing: true,
      orderMulti: false,
      order: [],
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null },
        { data: 'hostname' },
        { data: 'ip_addr_srv' },
        { data: 'username' },
        { data: 'functionality' },
        { data: 'platform_name' },
        { data: 'platform_id', visible: false },
        { data: 'description', visible: false },
        { data: 'action' }
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
            return `<span class="text-heading">${full['ip_addr_srv']}</span>`;
          }
        },
        {
          targets: 5,
          render: function (data, type, full, meta) {
            var username = full['username'];
            return '<span class="text-heading">' + username + '</span>';
          }
        },
        {
          targets: 6,
          render: function (data, type, full, meta) {
            var functionality = full['functionality'];

            return '<span class="text-heading">' + functionality + '</span>';
          }
        },
        {
          targets: 7,
          render: function (data, type, full) {
            return `<span class="text-heading">${full['platform_name']}</span>`;
          }
        },
        {
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: (data, type, full, meta) => {
            return `
      <div class="d-flex align-items-center">
        <a href="javascript:;" class="btn btn-sm btn-secondary text-white d-flex align-items-center gap-1 btn-generate-password" data-id="${full.id}" title="Generate">
          <i class="bx bx-key"></i> <small>Generate</small>
        </a>
      </div>
    `;
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
          rowClass: 'd-flex justify-content-end align-items-center gap-5 px-3 px-md-4',
          features: [
            {
              search: {
                placeholder: 'Cari Identity',
                text: '_INPUT_'
              }
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

        const createSearchableDropdown = (columnIndex, containerClass, selectId, placeholderText) => {
          const column = api.column(columnIndex);
          const select = document.createElement('select');
          select.id = selectId;
          select.className = 'form-select select2';
          select.innerHTML = `<option value="">${placeholderText}</option>`;
          document.querySelector(containerClass).appendChild(select);

          const searchWrapper = document.querySelector('.dt-search');
          if (searchWrapper && !document.querySelector('#requestEncryptBtn')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-primary ms-2';
            btn.id = 'requestEncryptBtn';
            btn.innerHTML = '<i class="bx bx-lock me-1"></i> Generate Terpilih';

            const wrapperBtn = document.createElement('div');
            wrapperBtn.className = 'd-flex align-items-center ms-2';
            wrapperBtn.appendChild(btn);
            searchWrapper.parentElement.appendChild(wrapperBtn);

            btn.addEventListener('click', function () {
              const selectedIds = dt_user.rows({ selected: true }).data().pluck('id').toArray();

              if (selectedIds.length === 0) {
                Swal.fire('Tidak Ada Data', 'Pilih minimal satu identity untuk generate password.', 'warning');
                return;
              }

              Swal.fire({
                title: 'Generate Password',
                text: `Yakin ingin generate dan enkripsi ${selectedIds.length} password?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
              }).then(result => {
                if (result.isConfirmed) {
                  Swal.fire({
                    title: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                  });

                  fetch('/vault/generate-password', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ identity_ids: selectedIds })
                  })
                    .then(res => res.json())
                    .then(result => {
                      Swal.close();
                      if (result.status === 'ok') {
                        let html = result.results
                          .map(
                            row => `
                              <div class="mb-2">
                               <span style="display:inline-block; font-size: 1rem; font-weight: bold; font-family: monospace; padding: 4px 10px; border-radius: 6px; margin-top: 4px;">${row.message}</span>
                              </div>
                            `
                          )
                          .join('');

                        Swal.fire({
                          title: 'Hasil Generate',
                          html,
                          width: 600,
                          confirmButtonText: 'Tutup'
                        });
                      } else {
                        Swal.fire('Gagal', result.message || 'Terjadi kesalahan saat generate.', 'error');
                      }
                    });
                }
              });
            });
          }

          // Isi awal
          const uniqueValues = [...new Set(column.data().toArray())].sort();
          uniqueValues.forEach(val => {
            const option = document.createElement('option');
            option.value = val;
            option.textContent = val;
            select.appendChild(option);
          });

          $(select).select2({
            placeholder: placeholderText,
            width: '100%'
          });

          $(select).on('change', function () {
            const val = this.value ? `^${this.value}$` : '';
            column.search(val, true, false).draw();
          });
        };

        const updateSearchableDropdown = (selectId, columnIndex, key) => {
          const select = $(`#${selectId}`);
          const column = api.column(columnIndex);
          const selectedValue = select.val();
          const placeholder = select.find('option:first').text();

          const filteredData = api.rows({ search: 'applied' }).data().toArray();
          const uniqueValues = [...new Set(filteredData.map(r => r[key]))].sort();

          // Rebuild options safely for Select2
          select.empty().append(`<option value="">${placeholder}</option>`);
          uniqueValues.forEach(val => {
            select.append(`<option value="${val}">${val}</option>`);
          });

          if (selectedValue && uniqueValues.includes(selectedValue)) {
            select.val(selectedValue);
          }

          select.trigger('change.select2'); // force update Select2 UI
        };

        // Inisialisasi dropdown awal
        createSearchableDropdown(5, '.server_username', 'ServerUsername', 'Pilih Username');
        createSearchableDropdown(6, '.server_functionality', 'ServerFunctionality', 'Pilih Functionality');
        createSearchableDropdown(7, '.server_platform', 'ServerPlatform', 'Pilih Platform');

        // Update saat redraw
        api.on('draw', function () {
          updateSearchableDropdown('ServerUsername', 5, 'username');
          updateSearchableDropdown('ServerFunctionality', 6, 'functionality');
          updateSearchableDropdown('ServerPlatform', 7, 'platform_name');
        });

        // Clear filter
        document.getElementById('clearFilterBtn')?.addEventListener('click', function () {
          $('#ServerUsername, #ServerFunctionality, #ServerPlatform').val('').trigger('change');
          api.columns().search('').draw();
        });
      }
    });

    $(document).on('click', '.btn-generate-password', function () {
      const identityId = $(this).data('id');
      const csrf = $('meta[name="csrf-token"]').attr('content');

      Swal.fire({
        title: 'Generate Password Baru?',
        text: 'Password lama akan diganti secara otomatis di server target.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Generate',
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: 'btn btn-primary me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          // ✅ Tampilkan loading sebelum AJAX
          Swal.fire({
            title: 'Sedang memproses...',
            text: 'Password sedang digenerate dan dienkripsi.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          $.ajax({
            url: '/vault/generate-password',
            method: 'POST',
            data: JSON.stringify({ identity_ids: [identityId] }),
            contentType: 'application/json',
            headers: {
              'X-CSRF-TOKEN': csrf
            },
            success: function (res) {
              Swal.close();
              const result = res.results?.[0];
              if (res.status === 'ok' && result?.status === 'success') {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil',
                  text: result.message
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: result?.message || res.message || 'Terjadi kesalahan.'
                });
              }
            },
            error: function (xhr) {
              Swal.close();
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memproses generate password.'
              });
              console.error(xhr.responseText);
            }
          });
        }
      });
    });

    // To remove default btn-secondary in export buttons
    $('.dt-buttons > .btn-group > button').removeClass('btn-secondary');
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

      // ✅ Tambah padding di wrapper atas
      { selector: '.dt-layout-top', classToAdd: 'px-3 px-md-4' },

      // ✅ Table tetap full
      { selector: '.dt-layout-full', classToAdd: 'table-responsive' }
    ];

    elementsToModify.forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(element => {
        if (classToRemove) {
          classToRemove.split(' ').forEach(className => element.classList.remove(className));
        }
        if (classToAdd) {
          classToAdd.split(' ').forEach(className => element.classList.add(className));
        }
      });
    });
  }, 100);
});
