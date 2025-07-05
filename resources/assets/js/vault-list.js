/**
 * Page Vault List (Client-side)
 */

'use strict';

let dtVault;

document.addEventListener('DOMContentLoaded', function () {
  const dtTable = document.querySelector('.datatables-users');

  const showToast = (type, msg) => {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: msg,
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
  };

  if (dtTable) {
    dtVault = new DataTable(dtTable, {
      ajax: {
        url: '/vault/data',
        data: function (d) {
          d.status = document.querySelector('#filter-status')?.value;
          d.user_name = document.querySelector('#filter-user')?.value;
          d.date_range = document.querySelector('#filter-date-range')?.value;
        }
      },
      processing: true,
      serverSide: true,
      order: [],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2) input[type="checkbox"]'
      },
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: function (row) {
              const data = row.data();
              return 'Detail Permintaan: ' + data['purpose'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            const data = columns
              .map(function (col) {
                return col.title !== ''
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
      columns: [
        { data: 'id' },
        { data: 'id', orderable: false, render: DataTable.render.select() },
        { data: null },
        { data: 'request_id' },
        { data: 'user.name' },
        { data: 'created_at' },
        { data: 'duration' },
        {
          data: 'status',
          name: 'status',
          render: function (data) {
            let badgeClass = '';
            let statusText = data.charAt(0).toUpperCase() + data.slice(1);

            switch (data) {
              case 'Approved':
                badgeClass = 'bg-label-success';
                break;
              case 'Rejected':
                badgeClass = 'bg-label-danger';
                break;
              case 'Pending':
                badgeClass = 'bg-label-warning';
                break;
              default:
                badgeClass = 'bg-label-info';
            }

            return `<span class="badge ${badgeClass}">${statusText}</span>`;
          }
        },
        { data: 'id' }
      ],
      columnDefs: [
        {
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 1,
          targets: 0,
          render: function () {
            return '';
          }
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 2,
          checkboxes: true,
          render: function () {
            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
          },
          checkboxes: {
            selectAllRender: '<input type="checkbox" class="form-check-input">'
          }
        },
        {
          targets: 2,
          searchable: false,
          orderable: false,
          render: function (data, type, row, meta) {
            return meta.row + 1 + meta.settings._iDisplayStart;
          }
        },
        {
          targets: -1,
          orderable: false,
          searchable: false,
          render: (data, type, row) => `
              <div class="d-flex gap-2">
                <a class="btn btn-sm btn-info btn-view text-white" title="Lihat Detail" href="/vault/${row.id}">
                  <i class="icon-base bx bx-show"></i>
                </a>
                <button class="btn btn-sm btn-warning btn-edit-request text-white"
                        title="Edit Pengajuan"
                        data-id="${row.id}"
                        data-request_id="${row.request_id}"
                        data-purpose="${row.purpose}"
                        data-start_time="${row.start_time}"
                        data-end_time="${row.end_time}"
                        data-bs-toggle="modal"
                        data-bs-target="#editRequestModal">
                  <i class="icon-base bx bx-edit-alt"></i>
                </button>
                <button class="btn btn-sm btn-success btn-approve text-white" title="Approve" data-id="${row.id}">
                  <i class="icon-base bx bx-check"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-reject text-white" title="Reject" data-id="${row.id}">
                  <i class="icon-base bx bx-x"></i>
                </button>
                <button class="btn btn-sm btn-secondary btn-delete-request text-white" title="Hapus" data-id="${row.id}">
                  <i class="icon-base bx bx-trash"></i>
                </button>
              </div>
            `
        }
      ],
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
          rowClass: 'd-flex flex-wrap gap-4 justify-content-end align-items-center px-3 px-md-4 mb-3',
          features: [
            {
              search: {
                placeholder: 'Cari Pengajuan',
                text: '_INPUT_'
              }
            },
            {
              buttons: [
                {
                  extend: 'collection',
                  className: 'btn btn-primary dropdown-toggle',
                  text: `
                    <span class="d-flex align-items-center gap-2">
                      <i class="icon-base bx bx-task"></i>
                      <span class="d-none d-sm-inline-block">Tindakan Batch</span>
                    </span>
                  `,
                  buttons: [
                    {
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="bx bx-check-circle me-2"></i>Approve Terpilih
                        </span>
                      `,
                      className: 'dropdown-item',
                      action: function () {
                        const selectedData = dtVault.rows({ selected: true }).data().toArray();
                        if (!selectedData.length) {
                          Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada data terpilih',
                            text: 'Silakan pilih data untuk disetujui',
                            confirmButtonText: 'OK',
                            customClass: {
                              confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                          });
                          return;
                        }

                        const selectedIds = selectedData.map(row => row.id);
                        Swal.fire({
                          title: 'Setujui Permintaan Terpilih?',
                          icon: 'question',
                          showCancelButton: true,
                          confirmButtonText: 'Setujui',
                          cancelButtonText: 'Batal',
                          customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-label-secondary'
                          },
                          buttonsStyling: false
                        }).then(result => {
                          if (result.isConfirmed) {
                            fetch(`/vault/approve/multiple`, {
                              method: 'POST',
                              headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                              },
                              body: JSON.stringify({ ids: selectedIds })
                            })
                              .then(res => res.json())
                              .then(res => {
                                if (res.success) {
                                  showToast('success', 'Permintaan disetujui');
                                  dtVault.ajax.reload();
                                } else {
                                  showToast('error', res.message || 'Gagal menyetujui');
                                }
                              });
                          }
                        });
                      }
                    },
                    {
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="bx bx-x-circle me-2"></i>Reject Terpilih
                        </span>
                      `,
                      className: 'dropdown-item',
                      action: function () {
                        const selectedData = dtVault.rows({ selected: true }).data().toArray();
                        if (!selectedData.length) {
                          Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada data terpilih',
                            text: 'Silakan pilih data untuk disetujui',
                            confirmButtonText: 'OK',
                            customClass: {
                              confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                          });
                          return;
                        }

                        const selectedIds = selectedData.map(row => row.id);
                        Swal.fire({
                          title: 'Tolak Permintaan Terpilih?',
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonText: 'Tolak',
                          cancelButtonText: 'Batal',
                          customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-label-secondary'
                          },
                          buttonsStyling: false
                        }).then(result => {
                          if (result.isConfirmed) {
                            fetch(`/vault/reject/multiple`, {
                              method: 'POST',
                              headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                              },
                              body: JSON.stringify({ ids: selectedIds })
                            })
                              .then(res => res.json())
                              .then(res => {
                                if (res.success) {
                                  showToast('success', 'Permintaan ditolak');
                                  dtVault.ajax.reload();
                                } else {
                                  showToast('error', res.message || 'Gagal menolak');
                                }
                              });
                          }
                        });
                      }
                    },
                    {
                      text: `
                        <span class="d-flex align-items-center">
                          <i class="bx bx-trash me-2"></i>Hapus Terpilih
                        </span>
                      `,
                      className: 'dropdown-item',
                      action: function () {
                        const selectedData = dtVault.rows({ selected: true }).data().toArray();
                        if (!selectedData.length) {
                          Swal.fire({
                            icon: 'warning',
                            title: 'Tidak ada data terpilih',
                            text: 'Silakan pilih data untuk dihapus',
                            confirmButtonText: 'OK',
                            customClass: {
                              confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                          });
                          return;
                        }

                        const selectedIds = selectedData.map(row => row.id);
                        Swal.fire({
                          title: 'Hapus Permintaan Terpilih?',
                          icon: 'warning',
                          showCancelButton: true,
                          confirmButtonText: 'Hapus',
                          cancelButtonText: 'Batal',
                          customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-label-secondary'
                          },
                          buttonsStyling: false
                        }).then(result => {
                          if (result.isConfirmed) {
                            fetch(`/vault/delete/multiple`, {
                              method: 'POST',
                              headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                              },
                              body: JSON.stringify({ ids: selectedIds })
                            })
                              .then(res => res.json())
                              .then(res => {
                                if (res.success) {
                                  showToast('success', 'Permintaan dihapus');
                                  dtVault.ajax.reload();
                                } else {
                                  showToast('error', res.message || 'Gagal menghapus');
                                }
                              });
                          }
                        });
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
        search: '',
        searchPlaceholder: 'Search',
        processing: `
          <div class="d-flex justify-content-center align-items-center py-5">
            <div class="sk-fold">
              <div class="sk-fold-cube"></div>
              <div class="sk-fold-cube"></div>
              <div class="sk-fold-cube"></div>
              <div class="sk-fold-cube"></div>
            </div>
          </div>`,
        paginate: {
          next: '<i class="icon-base bx bx-chevron-right"></i>',
          previous: '<i class="icon-base bx bx-chevron-left"></i>',
          first: '<i class="icon-base bx bx-chevrons-left"></i>',
          last: '<i class="icon-base bx bx-chevrons-right"></i>'
        }
      },
      drawCallback: function () {
        bindApprovalButtons();

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
        $(document).off('click', '.btn-delete-request');

        // Bind ulang tombol hapus satuan
        $(document).on('click', '.btn-delete-request', function () {
          const id = $(this).data('id');
          Swal.fire({
            title: 'Yakin hapus request ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
              confirmButton: 'btn btn-danger',
              cancelButton: 'btn btn-label-secondary'
            }
          }).then(result => {
            if (result.isConfirmed) {
              fetch(`/vault/${id}`, {
                method: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
              })
                .then(res => res.json())
                .then(res => {
                  if (res.success) {
                    Swal.fire('Berhasil', 'Request berhasil dihapus', 'success');
                    dtVault.ajax.reload();
                  } else {
                    Swal.fire('Gagal', res.message || 'Gagal menghapus request', 'error');
                  }
                })
                .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus', 'error'));
            }
          });
        });
      }
    });

    function approveRequest(id, isBatch = false) {
      Swal.fire({
        title: isBatch ? 'Setujui Permintaan Terpilih?' : 'Setujui Permintaan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Setujui',
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/vault/${id}/approve`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
            .then(r => r.json())
            .then(res => {
              if (res.success) {
                showToast('success', 'Permintaan disetujui');
                dtVault.ajax.reload();
              } else {
                showToast('error', res.message || 'Gagal menyetujui');
              }
            });
        }
      });
    }

    function rejectRequest(id, isBatch = false) {
      Swal.fire({
        title: isBatch ? 'Tolak Permintaan Terpilih?' : 'Tolak Permintaan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          fetch(`/vault/${id}/reject`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
            .then(r => r.json())
            .then(res => {
              if (res.success) {
                showToast('success', 'Permintaan ditolak');
                dtVault.ajax.reload();
              } else {
                showToast('error', res.message || 'Gagal menolak');
              }
            });
        }
      });
    }

    function bindApprovalButtons() {
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-approve');
        if (btn) {
          approveRequest(btn.dataset.id);
        }
      });

      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-reject');
        if (btn) {
          rejectRequest(btn.dataset.id);
        }
      });
    }

    // Trigger reload DataTable saat filter berubah
    document.querySelector('#filter-status')?.addEventListener('change', () => dtVault.ajax.reload());
    function debounce(func, delay) {
      let timer;
      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
      };
    }

    // Terapkan debounce ke filter nama user
    document.querySelector('#filter-user')?.addEventListener(
      'keyup',
      debounce(function () {
        dtVault.draw(); // pastikan ini objek DataTable yang kamu pakai
      }, 500)
    );

    // Inisialisasi Daterangepicker
    if (window.jQuery) {
      $('#filter-date-range').daterangepicker({
        autoUpdateInput: false,
        locale: {
          format: 'YYYY-MM-DD',
          cancelLabel: 'Clear'
        }
      });

      $('#filter-date-range').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        dtVault.ajax.reload();
      });

      $('#filter-date-range').on('cancel.daterangepicker', function () {
        $(this).val('');
        dtVault.ajax.reload();
      });
    }

    // Tombol Clear Filter
    document.querySelector('#btn-clear-filter')?.addEventListener('click', function () {
      // Reset nilai semua filter
      document.querySelector('#filter-status').value = '';
      document.querySelector('#filter-user').value = '';
      document.querySelector('#filter-date-range').value = '';

      // Reset daterangepicker UI
      $('#filter-date-range').data('daterangepicker')?.setStartDate(moment());
      $('#filter-date-range').data('daterangepicker')?.setEndDate(moment());
      $('#filter-date-range').val('');

      // Reload datatable tanpa filter
      dtVault.ajax.reload();
    });

    document.addEventListener('DOMContentLoaded', function () {
      // Inisialisasi Select2
      if (window.jQuery) {
        $('.select2').each(function () {
          const $this = $(this);
          $this.wrap('<div class="position-relative"></div>').select2({
            dropdownParent: $this.parent(),
            placeholder: 'Pilih salah satu'
          });
        });
      }

      // === Tombol Edit Identity ===
      $(document).on('click', '.btn-edit-identity', function () {
        const $btn = $(this);

        $('#editIdentityId').val($btn.data('id'));
        $('#editHostname').val($btn.data('hostname'));
        $('#editIpAddress').val($btn.data('ip_addr_srv'));
        $('#editUsername').val($btn.data('username'));
        $('#editFunctionality').val($btn.data('functionality'));
        $('#editPlatform').val($btn.data('platform_id')).trigger('change');
        $('#editDescription').val($btn.data('description'));

        // Hitung karakter deskripsi
        const descLength = $btn.data('description')?.length || 0;
        $('#charCount').text(`${500 - descLength} karakter tersisa`);
      });

      // === Tombol Edit Vault Request ===
      $(document).on('click', '.btn-edit-request', function () {
        const $btn = $(this);

        $('#editRequestId').val($btn.data('id'));
        $('#editRequestIdentifier').val($btn.data('request_id'));
        $('#editPurpose').val($btn.data('purpose'));
        $('#editDurationRange').val($btn.data('duration'));
        $('#editStatus').val($btn.data('status'));
        $('#editApprovedBy').val($btn.data('approved_by'));
        $('#editApprovedAt').val($btn.data('approved_at'));
        $('#editRevealedBy').val($btn.data('revealed_by'));
        $('#editRevealedAt').val($btn.data('revealed_at'));
        $('#editRevealerIp').val($btn.data('reveal_ip'));
        $('#editRevokedAt').val($btn.data('revoked_at'));
      });
    });

    // Disable pointer event on sorting header
    document.querySelectorAll('.sorting, .dt-column-title[role="button"]').forEach(el => {
      el.style.pointerEvents = 'none';
      el.removeAttribute('role');
      el.classList.remove('sorting');
    });
  }
  setTimeout(() => {
    // Perbesar search box dan length dropdown
    document.querySelectorAll('.dt-search input.form-control-sm').forEach(input => {
      input.classList.remove('form-control-sm');
      input.classList.add('form-control');
      input.style.minWidth = '220px'; // perbesar lebar search
      input.style.marginRight = '1rem'; // jarak ke tombol
    });

    document.querySelectorAll('.dt-length select.form-select-sm').forEach(select => {
      select.classList.remove('form-select-sm');
      select.classList.add('form-select');
      select.style.minWidth = '80px';
      select.style.marginRight = '1rem';
    });

    document.querySelectorAll('.dt-layout-top .dt-layout-end').forEach(el => {
      el.classList.add('d-flex', 'gap-4', 'align-items-center');
    });

    document.querySelectorAll('.dt-layout-top .dt-layout-start').forEach(el => {
      el.classList.add('mb-2');
    });
  }, 100);
});
