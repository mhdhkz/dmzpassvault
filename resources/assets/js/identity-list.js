/**
 * Page User List
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
                <a href="javascript:;" class="btn btn-icon delete-record">
                  <i class="icon-base bx bx-trash icon-md"></i>
                </a>
                <a href="javascript:;" class="btn btn-icon btn-view-record" data-id="${full.id}">
                  <i class="icon-base bx bx-show icon-md"></i>
                </a>
                <a href="javascript:;" class="btn btn-icon btn-edit-identity"
                  data-bs-toggle="modal"
                  data-bs-target="#editIdentity"
                  data-id="${full.id}"
                  data-hostname="${full.hostname}"
                  data-ip_addr_srv="${full.ip_addr_srv}"
                  data-username="${full.username}"
                  data-functionality="${full.functionality}"
                  data-platform_id="${full.platform_id}"
                  data-description="${full.description ?? ''}">
                  <i class="icon-base bx bx-edit icon-md"></i>
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
                  text: '<i class="bx bx-trash"></i> Hapus Terpilih',
                  className: 'btn btn-danger delete-selected',
                  action: function () {
                    const selectedData = dt_user.rows({ selected: true }).data().toArray();
                    if (selectedData.length === 0) {
                      Swal.fire({
                        icon: 'warning',
                        title: 'Tidak ada data terpilih',
                        text: 'Silakan pilih setidaknya satu baris untuk dihapus.',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                      });
                      return;
                    }

                    Swal.fire({
                      title: 'Hapus Data Terpilih?',
                      text: 'Data yang dihapus tidak dapat dikembalikan.',
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonText: 'Ya, Hapus!',
                      cancelButtonText: 'Batal',
                      customClass: {
                        confirmButton: 'btn btn-danger mx-1',
                        cancelButton: 'btn btn-outline-secondary mx-1'
                      },
                      buttonsStyling: false
                    }).then(result => {
                      if (!result.isConfirmed) return;

                      const idsToDelete = selectedData.map(row => row.id);

                      fetch('/identity/delete/multiple', {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ids: idsToDelete })
                      })
                        .then(res => res.json())
                        .then(result => {
                          if (result.success) {
                            dt_user.ajax.reload();

                            // Toast-style SweetAlert
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

                            Toast.fire({
                              icon: 'success',
                              title: 'Data berhasil dihapus',
                              customClass: {
                                popup: 'colored-toast'
                              }
                            });
                          } else {
                            Swal.fire({
                              icon: 'error',
                              title: 'Gagal',
                              text: 'Data gagal dihapus.',
                              confirmButtonText: 'OK',
                              customClass: {
                                confirmButton: 'btn btn-danger'
                              },
                              buttonsStyling: false
                            });
                          }
                        })
                        .catch(err => {
                          console.error(err);
                          Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Silakan coba lagi nanti.',
                            confirmButtonText: 'OK',
                            customClass: {
                              confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
                          });
                        });
                    });
                  }
                }
              ]
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
                        columns: [3, 4, 5, 6, 7],
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
                        columns: [3, 4, 5, 6, 7],
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
                        columns: [3, 4, 5, 6, 7],
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
                        columns: [3, 4, 5, 6, 7],
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
                },
                {
                  text: '<i class="bx bx-plus icon-sm me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Tambah Identity Baru</span>',
                  className: 'btn btn-primary',
                  action: function () {
                    window.location.href = '/identity/identity-form';
                  }
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

        const createSearchableDropdown = (columnIndex, containerClass, selectId, placeholderText) => {
          const column = api.column(columnIndex);
          const select = document.createElement('select');
          select.id = selectId;
          select.className = 'form-select select2';
          select.innerHTML = `<option value="">${placeholderText}</option>`;
          document.querySelector(containerClass).appendChild(select);

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

    //? The 'delete-record' class is necessary for the functionality of the following code.
    function deleteRecord(event) {
      const button = event.target.closest('.delete-record');
      const rowElement = button.closest('tr');
      const rowData = dt_user.row(rowElement).data();
      const id = rowData?.id;

      if (!id) return;

      Swal.fire({
        title: 'Yakin hapus data ini?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: 'btn btn-danger mx-2',
          cancelButton: 'btn btn-outline-secondary mx-2'
        },
        buttonsStyling: false
      }).then(result => {
        if (!result.isConfirmed) return;

        fetch(`/identity/delete/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              dt_user.row(rowElement).remove().draw();

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

              Toast.fire({
                icon: 'success',
                title: 'Data berhasil dihapus'
              });
            } else {
              throw new Error(data.message || 'Gagal menghapus');
            }
          })
          .catch(err => {
            console.error(err);
            Swal.fire({
              icon: 'error',
              title: 'Gagal menghapus',
              text: err.message || 'Terjadi kesalahan saat menghapus data'
            });
          });
      });
    }

    function bindDeleteEvent() {
      const userListTable = document.querySelector('.datatables-users');
      const modal = document.querySelector('.dtr-bs-modal');

      if (userListTable && userListTable.classList.contains('collapsed')) {
        if (modal) {
          modal.addEventListener('click', function (event) {
            if (event.target.parentElement.classList.contains('delete-record')) {
              deleteRecord();
              const closeButton = modal.querySelector('.btn-close');
              if (closeButton) closeButton.click(); // Simulates a click on the close button
            }
          });
        }
      } else {
        const tableBody = userListTable?.querySelector('tbody');
        if (tableBody) {
          tableBody.addEventListener('click', function (event) {
            if (event.target.parentElement.classList.contains('delete-record')) {
              deleteRecord(event);
            }
          });
        }
      }
    }

    function bindViewEvent() {
      const tableBody = document.querySelector('.datatables-users tbody');
      if (tableBody) {
        tableBody.addEventListener('click', function (event) {
          const btn = event.target.closest('.btn-view-record');
          if (btn) {
            const id = btn.getAttribute('data-id');
            if (id) {
              window.location.href = `/identity/detail/${id}`;
            }
          }
        });
      }
    }

    function bindEditEvent() {
      const table = document.querySelector('.datatables-users');
      if (!table) return;

      table.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-identity');
        if (!btn) return;

        // Ambil data-* dari tombol
        const id = btn.dataset.id;
        const hostname = btn.dataset.hostname;
        const ip = btn.dataset.ip_addr_srv;
        const username = btn.dataset.username;
        const functionality = btn.dataset.functionality;
        const platformId = btn.dataset.platform_id;
        const description = btn.dataset.description;

        // Target modal dan form
        const modal = document.getElementById('editIdentity');

        // Prefill field modal
        modal.querySelector('#editIdentityId').value = id;
        modal.querySelector('#editHostname').value = hostname;
        modal.querySelector('#editIpAddress').value = ip;
        modal.querySelector('#editUsername').value = username;
        modal.querySelector('#editFunctionality').value = functionality;
        modal.querySelector('#editDescription').value = description;

        // Re-populate Select2 platform
        const select = modal.querySelector('#editPlatform');
        if (select && window.platformList && platformId) {
          select.innerHTML = '';
          for (const [key, value] of Object.entries(window.platformList)) {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = value;
            if (parseInt(key) === parseInt(platformId)) {
              opt.selected = true;
            }
            select.appendChild(opt);
          }

          // Re-init Select2
          $(select).select2({
            dropdownParent: $('#editIdentity'),
            width: '100%'
          });
        }
      });
    }

    // Initial event binding
    bindDeleteEvent();
    bindViewEvent();
    bindEditEvent();

    // Re-bind events when modal is shown or hidden
    document.addEventListener('show.bs.modal', function (event) {
      if (event.target.classList.contains('dtr-bs-modal')) {
        bindDeleteEvent();
      }
    });

    document.addEventListener('hide.bs.modal', function (event) {
      if (event.target.classList.contains('dtr-bs-modal')) {
        bindDeleteEvent();
      }
    });

    // To remove default btn-secondary in export buttons
    $('.dt-buttons > .btn-group > button').removeClass('btn-secondary');
  }

  // Handle form submit untuk edit identity
  document.querySelector('#editIdentityForm')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const id = form.querySelector('#editIdentityId').value;

    const data = {
      hostname: form.querySelector('#editHostname').value,
      ip_addr_srv: form.querySelector('#editIpAddress').value,
      username: form.querySelector('#editUsername').value,
      functionality: form.querySelector('#editFunctionality').value,
      platform_id: form.querySelector('#editPlatform').value,
      description: form.querySelector('#editDescription').value
    };

    fetch(`/identity/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(data)
    })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          $('#editIdentity').modal('hide');
          Swal.fire('Berhasil', 'Data berhasil diperbarui', 'success');
          dt_user.ajax.reload();
        } else {
          Swal.fire('Gagal', 'Gagal memperbarui data', 'error');
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan saat mengirim data', 'error');
      });
  });

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

    // Delete record
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
