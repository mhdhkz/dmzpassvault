'use strict';

// Tampilkan SweetAlert saat awal halaman sedang di-load
Swal.fire({
  title: 'Mohon Tunggu',
  text: 'Sedang memuat data halaman...',
  allowOutsideClick: false,
  allowEscapeKey: false,
  didOpen: () => Swal.showLoading()
});

// Tutup SweetAlert saat semua elemen selesai dimuat
window.addEventListener('load', () => {
  setTimeout(() => {
    Swal.close();
  }, 1100);
});

document.addEventListener('DOMContentLoaded', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  const isDataReady = btn => btn?.dataset?.id && btn?.dataset?.name;

  // ==================[ DELETE USER ]==================
  document.body.addEventListener('click', function (e) {
    const deleteBtn = e.target.closest('.delete-user');
    if (!deleteBtn) return;

    const id = deleteBtn.getAttribute('data-id');

    Swal.fire({
      title: 'Apakah kamu yakin?',
      text: 'Data yang dihapus tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(result => {
      if (!result.isConfirmed) return;

      fetch(`/user-list/delete/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          Accept: 'application/json'
        }
      })
        .then(res => {
          if (!res.ok) throw new Error('Gagal menghapus');
          return res.json();
        })
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: 'Data telah dihapus.',
              showConfirmButton: false,
              timer: 1500
            }).then(() => {
              window.location.href = '/admin/user-list';
            });
          } else {
            throw new Error(data.message || 'Gagal menghapus');
          }
        })
        .catch(err => {
          Swal.fire('Error', err.message, 'error');
        });
    });
  });

  // ==================[ FUNGSI ISI FORM EDIT ]==================
  function isiFormEditUser(btn, form) {
    form.querySelector('#editUserDetailId').value = btn.dataset.id;
    form.querySelector('#editUserDetailName').value = btn.dataset.name;
    form.querySelector('#editUserDetailEmail').value = btn.dataset.email;
    form.querySelector('#editUserDetailRole').value = btn.dataset.role;
    form.querySelector('#editUserDetailBirthDate').value = btn.dataset.birth_date ?? '';
    form.querySelector('#editUserDetailNationality').value = btn.dataset.nationality ?? '';
    form.querySelector('#editUserDetailEmployeeId').value = btn.dataset.employee_id ?? '';
    form.querySelector('#editUserDetailJobTitle').value = btn.dataset.job_title ?? '';
    const position = btn.dataset.position;
    try {
      const parsedPosition = JSON.parse(position);
      form.querySelector('#editUserDetailPosition').value = parsedPosition.name ?? '';
    } catch (e) {
      // Fallback jika bukan JSON
      form.querySelector('#editUserDetailPosition').value = position ?? '';
    }
    form.querySelector('#editUserDetailWorkMode').value = btn.dataset.work_mode ?? '';
    form.querySelector('#editUserDetailWorkLocation').value = btn.dataset.work_location ?? '';
  }

  // ==================[ FUNGSI ISI FORM PASSWORD ]==================
  function isiFormPassword(btn, form, label) {
    form.reset();
    form.querySelector('#changePasswordUserId').value = btn.dataset.id;
    if (label) label.textContent = btn.dataset.name;
  }

  // ==================[ TOMBOL EDIT USER ]==================
  document.querySelectorAll('.btn-edit-user-detail').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('editUserDetailForm');
      isiFormEditUser(this, form);
    });
  });

  // ==================[ TOMBOL CHANGE PASSWORD ]==================
  document.querySelectorAll('.btn-password-user').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = document.getElementById('changePasswordForm');
      const label = document.getElementById('changePasswordUsername');
      isiFormPassword(this, form, label);
    });
  });

  // ==================[ SUBMIT EDIT FORM ]==================
  const editForm = document.getElementById('editUserDetailForm');
  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(editForm);
      const id = formData.get('id');

      fetch(`/admin/user-list/${id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf },
        body: formData
      })
        .then(res => res.json())
        .then(res => {
          if (res.success) {
            if (res.email_changed && res.is_self) {
              Swal.fire({
                icon: 'success',
                title: 'Email berhasil diubah',
                text: 'Kamu akan logout dan diminta login ulang.',
                confirmButtonText: 'OK'
              }).then(() => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                form.innerHTML = `<input type="hidden" name="_token" value="${csrf}">`;
                document.body.appendChild(form);
                form.submit();
              });
              return;
            }

            if (res.role_changed && res.is_self) {
              Swal.fire({
                icon: 'info',
                title: 'Perubahan Role',
                text: 'Role kamu telah diubah. Akan diarahkan ke dashboard.',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                window.location.href = '/dashboard';
              });
              return;
            }

            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: 'Data berhasil diperbarui.',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire('Gagal', res.message || 'Gagal memperbarui data', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
        });
    });
  }
});
