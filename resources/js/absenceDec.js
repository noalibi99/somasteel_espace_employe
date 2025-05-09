document.addEventListener('DOMContentLoaded', function () {
    // --- Employee Search Filter ---
const searchInput = document.getElementById('searchInput');
const resTable = document.getElementById('shift-table-responsable');

if (searchInput && resTable) {
    searchInput.addEventListener('input', function () {
        const filter = searchInput.value.toLowerCase();
        const rows = resTable.querySelectorAll('tbody tr');
        rows.forEach(row => {
            // 2nd column: Nom & Prénom
            const nameCell = row.children[1];
            if (nameCell && nameCell.textContent.toLowerCase().includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

const searchInputRH = document.getElementById('searchInputRH');
    const laminoireTable = document.getElementById('shift-table-laminoire');
    const acierieTable = document.getElementById('shift-table-acierie');

    function filterTable(table, filter) {
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            // 2nd column: Nom & Prénom
            const nameCell = row.children[1];
            if (nameCell && nameCell.textContent.toLowerCase().includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInputRH) {
        searchInputRH.addEventListener('input', function () {
            const filter = searchInputRH.value.toLowerCase();

            // Only filter the visible table
            if (laminoireTable && !laminoireTable.closest('.shift-card-body').classList.contains('hidden')) {
                filterTable(laminoireTable, filter);
            }
            if (acierieTable && !acierieTable.closest('.shift-card-body').classList.contains('hidden')) {
                filterTable(acierieTable, filter);
            }
        });
    }


   // --- Settings Dropdown ---
   const dropdownBtn = document.getElementById('settingsDropdownBtn');
   const dropdown = document.getElementById('settingsDropdown');
   if (dropdownBtn && dropdown) {
       dropdownBtn.addEventListener('click', function (e) {
           e.stopPropagation();
           dropdown.classList.toggle('hidden');
       });
       document.addEventListener('click', function () {
           dropdown.classList.add('hidden');
       });
       dropdown.addEventListener('click', function (e) {
           e.stopPropagation();
       });
   }

   // --- Shift Switch Buttons (NO nested DOMContentLoaded!) ---
   document.querySelectorAll('.shift-btn').forEach(btn => {
       btn.addEventListener('click', function () {
           document.querySelectorAll('.shift-btn').forEach(b => {
               b.classList.remove('bg-white', 'text-orange-500', 'border', 'border-orange-500', 'active');
               b.classList.add('bg-orange-500', 'text-white');
           });
           this.classList.remove('bg-orange-500', 'text-white');
           this.classList.add('bg-white', 'text-orange-500', 'border', 'border-orange-500', 'active');

           document.querySelectorAll('.shift-card-body').forEach(body => {
               body.classList.add('hidden');
           });
           const shiftId = this.getAttribute('data-shift-id');
           const target = document.getElementById(shiftId);
           if (target) target.classList.remove('hidden');
       });
   });

   // --- Modal Open/Close ---
   const openModalBtn = document.getElementById('openShiftModal');
   const closeModalBtns = [
       document.getElementById('closeShiftModal'),
       document.getElementById('closeShiftModalFooter')
   ];
   const modal = document.getElementById('editShiftModal');
   if (openModalBtn && modal) {
       openModalBtn.addEventListener('click', function () {
           modal.classList.remove('hidden');
       });
   }
   closeModalBtns.forEach(btn => {
       if (btn) {
           btn.addEventListener('click', function () {
               modal.classList.add('hidden');
           });
       }
   });
   // ESC to close modal
   document.addEventListener('keydown', function (e) {
       if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
           modal.classList.add('hidden');
       }
   });

   // --- Tab Switching ---
   document.querySelectorAll('.shift-tab').forEach(tab => {
       tab.addEventListener('click', function (e) {
           e.preventDefault();
           document.querySelectorAll('.shift-tab').forEach(t => t.classList.remove('active', 'bg-orange-100'));
           this.classList.add('active', 'bg-orange-100');
           const shiftId = this.getAttribute('data-shift-id');
           document.querySelectorAll('.shift-card-body').forEach(body => {
               body.classList.add('d-none');
           });
           const target = document.getElementById(shiftId);
           if (target) target.classList.remove('d-none');
       });
   });

   // --- Table Filters ---
   document.querySelectorAll('.service-filter, .presence-filter').forEach(filter => {
       filter.addEventListener('change', function () {
           const tableId = this.getAttribute('data-table');
           const serviceFilter = document.getElementById('service-filter-' + tableId).value.toLowerCase();
           const presenceFilter = document.getElementById('presence-filter-' + tableId).value.toLowerCase();
           const rows = document.querySelectorAll(`#shift-table-${tableId} tbody tr`);
           rows.forEach(row => {
               const service = row.children[2].textContent.toLowerCase();
               const presence = row.children[3].textContent.toLowerCase();
               const show = (serviceFilter === "" || service.includes(serviceFilter)) &&
                            (presenceFilter === "" || presence.includes(presenceFilter));
               row.style.display = show ? "" : "none";
           });
       });
   });

   // --- Clock ---
   function updateClock() {
       const now = new Date();
       const timeElem = document.querySelector('.time');
       const dateElem = document.querySelector('.date');
       if (timeElem) timeElem.textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
       if (dateElem) dateElem.textContent = now.toLocaleDateString();
   }
   setInterval(updateClock, 1000);
   updateClock();

   // --- Attendance Status Buttons (Responsable) ---
   document.querySelectorAll('.btn-status').forEach(button => {
       button.addEventListener('click', function () {
           const userId = this.getAttribute('data-user-id');
           const status = this.getAttribute('data-status');
           const group = document.getElementById('attendance-buttons-' + userId);
           if (group) {
               group.querySelectorAll('.btn-status').forEach(btn => {
                   btn.classList.remove('bg-green-500', 'bg-red-500', 'text-white', 'bg-gray-200', 'text-black');
                   btn.classList.add('bg-gray-200', 'text-black');
               });
               this.classList.remove('bg-gray-200', 'text-black');
               if (status === 'Présent') {
                   this.classList.add('bg-green-500', 'text-white');
               } else {
                   this.classList.add('bg-red-500', 'text-white');
               }
           }
           const input = document.getElementById('status-' + userId);
           if (input) input.value = status;
       });
   });

   // --- Shift Modal CRUD (Vanilla AJAX) ---
   // You may need to adapt the URLs and CSRF token logic to your backend
   const shiftTableBody = document.getElementById('shiftTableBody');
   const shiftForm = document.getElementById('shiftForm');
   const nameInput = document.getElementById('name');
   const startTimeInput = document.getElementById('start_time');
   const endTimeInput = document.getElementById('end_time');
   const saveShiftBtn = document.getElementById('saveShift');
   console.log('saveShiftBtn:', saveShiftBtn);
   const shiftIdInput = document.getElementById('shift_id');
   const shiftsRouteMeta = document.querySelector('meta[name="shifts-route"]');
   const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
   const shiftsUrl = shiftsRouteMeta ? shiftsRouteMeta.getAttribute('content') : null;
   const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

   function loadShifts() {
    if (!shiftsUrl || !shiftTableBody) return;
    fetch(shiftsUrl)
        .then(response => response.json())
        .then(data => {
            shiftTableBody.innerHTML = '';
            data.forEach(shift => {
                const tr = document.createElement('tr');
                tr.className = "hover: border-b border-gray-100";
                tr.innerHTML = `
                    <td class="py-2 px-4">${shift.name}</td>
                    <td class="py-2 px-4">${shift.start_time}</td>
                    <td class="py-2 px-4">${shift.end_time}</td>
                    <td class="py-2 px-4">
                        <button type="button"
            class="editShift bg-white text-orange-600 border border-orange-300 rounded px-2 py-1 mr-2 transition hover:bg-orange-100 hover:text-orange-800 hover:shadow"
            data-id="${shift.id}">
            Edit
        </button>
        <button type="button"
            class="deleteShift bg-white text-red-600 border border-red-300 rounded px-2 py-1 transition hover:bg-red-100 hover:text-red-800 hover:shadow"
            data-id="${shift.id}">
            Delete
        </button>
                    </td>
                `;
                shiftTableBody.appendChild(tr);
            });
        });
}
   if (shiftTableBody) loadShifts();

   // Edit shift
   let shiftIdToDelete = null;

   // Listen for delete button clicks
   if (shiftTableBody) {
       shiftTableBody.addEventListener('click', function (e) {
           if (e.target.classList.contains('editShift')) {
               const id = e.target.getAttribute('data-id');
               fetch(`${shiftsUrl}/${id}`)
                   .then(response => response.json())
                   .then(shift => {
                       shiftIdInput.value = shift.id;
                       nameInput.value = shift.name;
                       startTimeInput.value = shift.start_time;
                       endTimeInput.value = shift.end_time;
                   });
           }
           if (e.target.classList.contains('deleteShift')) {
               shiftIdToDelete = e.target.getAttribute('data-id');
               document.getElementById('deleteModal').classList.remove('hidden');
           }
       });
   }
   
   // Modal buttons
   const deleteModal = document.getElementById('deleteModal');
   const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
   const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
   
   if (cancelDeleteBtn) {
       cancelDeleteBtn.addEventListener('click', function () {
           deleteModal.classList.add('hidden');
           shiftIdToDelete = null;
       });
   }
   
   if (confirmDeleteBtn) {
       confirmDeleteBtn.addEventListener('click', function () {
           if (shiftIdToDelete) {
               fetch(`${shiftsUrl}/${shiftIdToDelete}`, {
                   method: 'DELETE',
                   headers: {
                       'X-CSRF-TOKEN': csrfToken,
                       'Accept': 'application/json'
                   }
               }).then(() => {
                   loadShifts();
                   deleteModal.classList.add('hidden');
                   shiftIdToDelete = null;
                   showToast('Shift supprimé avec succès !', 'success');
               });
           }
       });
   }
   
   // Optional: Close modal when clicking outside the modal content
   if (deleteModal) {
       deleteModal.addEventListener('click', function(e) {
           if (e.target === deleteModal) {
               deleteModal.classList.add('hidden');
               shiftIdToDelete = null;
           }
       });
   }

   // Save shift (add or update)
   if (saveShiftBtn && shiftForm) {
      console.log('Attaching click handler to saveShiftBtn');
    saveShiftBtn.addEventListener('click', function () {
        console.log('Save button clicked!');
         
          const id = shiftIdInput.value;
          const url = id ? `${shiftsUrl}/${id}` : shiftsUrl;
          const method = id ? 'POST' : 'POST'; // Always POST for web.php, use _method for PUT
  
          // Build form data
          const formData = new URLSearchParams();
          formData.append('name', nameInput.value);
          formData.append('start_time', startTimeInput.value);
          formData.append('end_time', endTimeInput.value);
          formData.append('_token', csrfToken);
          if (id) formData.append('_method', 'PUT'); // Method spoofing for update
  
          fetch(url, {
              method: method,
              headers: {
                  'Accept': 'application/json'
              },
              body: formData
          })
          .then(response => {
              if (!response.ok) {
                  return response.json().then(err => { throw err; });
              }
              return response.json();
          })
          .then(() => {
              shiftForm.reset();
              shiftIdInput.value = '';
              loadShifts();
              showToast(id ? 'Shift modifié avec succès !' : 'Shift ajouté avec succès !', 'success');
          })
          .catch(error => {
              console.error('Save error:', error);
              showToast('Erreur lors de la sauvegarde du shift.', 'error');
          });
      });
  }

   // Auto-generate shift name
   if (startTimeInput && endTimeInput && nameInput) {
       function updateShiftName() {
           if (startTimeInput.value && endTimeInput.value) {
               nameInput.value = `[${startTimeInput.value} - ${endTimeInput.value}]`;
           }
       }
       startTimeInput.addEventListener('change', updateShiftName);
       endTimeInput.addEventListener('change', updateShiftName);
   }

   window.showToast = function(message, type = 'success') {
    const toaster = document.getElementById('toaster');
    const toasterMessage = document.getElementById('toaster-message');
    const toasterContent = document.getElementById('toaster-content');
    if (!toaster || !toasterMessage || !toasterContent) return;
    toasterMessage.textContent = message;

    // Set color based on type
    if (type === 'success') {
        toasterContent.className = 'bg-orange-500 text-white px-4 py-2 rounded shadow-lg flex items-center space-x-2';
    } else if (type === 'error') {
        toasterContent.className = 'bg-red-500 text-white px-4 py-2 rounded shadow-lg flex items-center space-x-2';
    } else {
        toasterContent.className = 'bg-gray-700 text-white px-4 py-2 rounded shadow-lg flex items-center space-x-2';
    }

    toaster.classList.remove('hidden');
    setTimeout(() => {
        toaster.classList.add('hidden');
    }, 2500);
}
});