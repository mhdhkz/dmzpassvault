/**
 * Page Vault Decrypt
 */

'use strict';

function escapeHtml(unsafe) {
  return unsafe
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

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
        <a href="javascript:;" class="btn btn-sm btn-secondary text-white d-flex align-items-center gap-1 btn-decrypt-password" data-id="${full.id}" title="Lihat Password">
          <i class="bx bx-key"></i> <small>Lihat Password</small>
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
          if (searchWrapper && !document.querySelector('#requestDecryptBtn')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-primary ms-2';
            btn.id = 'requestDecryptBtn';
            btn.innerHTML = '<i class="bx bx-lock-open-alt me-1"></i> Dekripsi Terpilih';

            const wrapperBtn = document.createElement('div');
            wrapperBtn.className = 'd-flex align-items-center ms-2';
            wrapperBtn.appendChild(btn);
            searchWrapper.parentElement.appendChild(wrapperBtn);

            btn.addEventListener('click', function () {
              const selectedIds = dt_user.rows({ selected: true }).data().pluck('id').toArray();

              if (selectedIds.length === 0) {
                Swal.fire('Tidak Ada Data', 'Pilih minimal satu identity untuk dekripsi.', 'warning');
                return;
              }

              Swal.fire({
                title: 'Dekripsi Password',
                text: `Yakin ingin mendekripsi ${selectedIds.length} password?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Dekripsi',
                cancelButtonText: 'Batal'
              }).then(result => {
                if (result.isConfirmed) {
                  Swal.fire({
                    title: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                  });

                  fetch('/vault/decrypt/multiple', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: selectedIds })
                  })
                    .then(res => res.json())
                    .then(result => {
                      Swal.close();
                      if (result.status === 'ok') {
                        const html = result.data
                          .map(item => {
                            if (item.password.startsWith('[Error')) {
                              return `
                                  <p style="margin: 0.3rem 0;">
                                    <span style="color:crimson;">${escapeHtml(item.password)}</span>
                                  </p>`;
                            } else {
                              return `
                                  <p style="margin: 0.3rem 0;">
                                    <span style=font-size: 1rem">${item.hostname}:</span>
                                    <span style="display:inline-block; font-size: 1rem; font-weight: bold; font-family: monospace; background: #f3f3f3; padding: 4px 10px; border-radius: 6px; margin-top: 4px;">
                                      ${escapeHtml(item.password)}
                                    </span>
                                  </p>`;
                            }
                          })
                          .join('');

                        Swal.fire({
                          title: 'Hasil Dekripsi',
                          html: `
                            <div id="decryptResults">
                              ${html}
                            </div>
                            <div class="mt-3">
                              <button class="btn btn-sm btn-primary" id="copyAllPasswordsBtn">
                                <i class="bx bx-copy-alt me-1"></i> Salin Semua Password
                              </button>
                              <span id="copyAllSuccess" style="display:none; margin-left: 10px; color: green;">Disalin!</span>
                            </div>
                          `,
                          width: 700,
                          confirmButtonText: 'Tutup',
                          didOpen: () => {
                            document.getElementById('copyAllPasswordsBtn')?.addEventListener('click', () => {
                              const passList = Array.from(document.querySelectorAll('#decryptResults span'))
                                .map(el => el.textContent.trim())
                                .filter(p => !p.startsWith('[Error')); // exclude error
                              const allText = passList.join('\n');
                              navigator.clipboard.writeText(allText).then(() => {
                                const copied = document.getElementById('copyAllSuccess');
                                copied.style.display = 'inline';
                                setTimeout(() => (copied.style.display = 'none'), 2000);
                              });
                            });
                          }
                        });
                      } else {
                        Swal.fire('Gagal', result.message || 'Terjadi kesalahan saat dekripsi.', 'error');
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

    document.addEventListener('click', function (e) {
      const btn = e.target.closest('.btn-decrypt-password');
      if (btn) {
        Swal.fire({
          title: 'Mengambil password...',
          html: 'Mohon tunggu sebentar.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        const id = btn.getAttribute('data-id');
        if (id) {
          fetch(`/vault/check-access/${id}`, {
            method: 'GET',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
            .then(res => res.json())
            .then(data => {
              if (data.status === 'ok') {
                // Jika check-access sukses, lanjut fetch password
                fetch(`/vault/decrypt-password/${id}`, {
                  method: 'GET',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                  }
                })
                  .then(res => res.json())
                  .then(decrypt => {
                    if (decrypt.status === 'ok') {
                      Swal.fire({
                        icon: 'info',

                        html: `
                            <div style="font-size: 1.2rem; color: #6c757d; margin-bottom: 0.5rem;">
                              Password untuk <strong>${decrypt.hostname}</strong>
                            </div>
                            <div style="font-size: 2.2rem; font-family: monospace; background: #f1f1f1; padding: 6px 10px; border-radius: 6px; display: inline-block;" id="passwordText">
                            </div>
                            <div class="mt-3">
                              <button class="btn btn-sm btn-primary" id="copyPasswordBtn">
                                <i class="bx bx-copy-alt me-1"></i> Salin Password
                              </button>
                              <span id="copySuccess" style="display:none; margin-left: 10px; color: green;">Disalin!</span>
                            </div>
                            <div class="mt-2" style="font-size: 0.8rem; color: #999;">
                              Akan tertutup otomatis dalam <span id="countdown">15</span> detik
                            </div>
                          `,
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup',
                        allowOutsideClick: false,
                        timer: 15000, // ✅ ini yang menjamin close otomatis setelah 15 detik
                        didOpen: () => {
                          document.getElementById('passwordText').textContent = decrypt.decrypted_password;
                          // Tombol Copy
                          document.getElementById('copyPasswordBtn').addEventListener('click', () => {
                            const password = document.getElementById('passwordText').innerText;
                            navigator.clipboard.writeText(password).then(() => {
                              const copied = document.getElementById('copySuccess');
                              copied.style.display = 'inline';
                              setTimeout(() => (copied.style.display = 'none'), 2000);
                            });
                          });

                          // Manual countdown sync
                          let seconds = 15;
                          const countdown = document.getElementById('countdown');
                          const timerInterval = setInterval(() => {
                            seconds--;
                            countdown.textContent = seconds;
                            if (seconds <= 0) {
                              clearInterval(timerInterval);
                            }
                          }, 1000);
                        }
                      });
                    } else {
                      Swal.fire('Gagal', decrypt.message || 'Password tidak ditemukan', 'error');
                    }
                  });
              } else {
                Swal.fire('Akses Ditolak', data.message || 'Request belum disetujui atau sudah kadaluarsa.', 'warning');
              }
            })
            .catch(err => {
              Swal.fire('Gagal', err.message || 'Terjadi kesalahan', 'error');
            });
        }
      }
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
