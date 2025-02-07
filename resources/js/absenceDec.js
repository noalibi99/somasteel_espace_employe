$(document).ready(function() {
   $('table.shift-table').DataTable({
      "language": {
          "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
      },
      searching: true,
      paging: false,
      responsive: true,
      dom: '<"top"f<i>>rt<"bottom"lp><"clear">' // Moves search input and info text to the top
  });
  
   function filterTable(tableId) {
      var serviceFilter = $('#service-filter-' + tableId).val().toLowerCase();
      var presenceFilter = $('#presence-filter-' + tableId).val().toLowerCase();
  
      $('#shift-table-' + tableId + ' tbody tr').each(function () {  // Fixed table ID selector
          var row = $(this);
          var service = row.find('td').eq(2).text().toLowerCase(); // Service column
          var presence = row.find('td').eq(3).text().trim().toLowerCase(); // Présence column
  
          var isServiceMatch = serviceFilter === "" || service.indexOf(serviceFilter) !== -1;
          var isPresenceMatch = presenceFilter === "" || presence.indexOf(presenceFilter) !== -1;
  
          row.toggle(isServiceMatch && isPresenceMatch);
      });
  }
  
  $('.service-filter, .presence-filter').on('change', function () {
   var tableId = $(this).data('table'); // Get correct table ID
   filterTable(tableId); // Pass the correct table ID
});

// shifts management 
loadShifts();

   function loadShifts() {
      let shiftsUrl = $('meta[name="shifts-route"]').attr("content"); // Get the route from meta tag
      $.get(shiftsUrl, function (data) {
         let tableRows = "";
         $.each(data, function (index, shift) {
            tableRows += `
                  <tr>
                     <td>${shift.name}</td>
                     <td>${shift.start_time}</td>
                     <td>${shift.end_time}</td>
                     <td>
                        <button type="button" class="btn btn-warning btn-sm editShift" data-id="${shift.id}">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm deleteShift" data-id="${shift.id}">Delete</button>
                     </td>
                  </tr>`;
         });
         $("#shiftTableBody").html(tableRows);
      });
   }

   $.ajaxSetup({
      headers: {
         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
   });
   $(document).on("click", ".editShift", function () {
      let id = $(this).data("id");
      let shiftsUrl = $('meta[name="shifts-route"]').attr("content"); // Get base URL from meta tag
      let url = shiftsUrl + "/" + id; // Construct the update URL dynamically
  
      $.get(url, function (shift) {
          $("#shift_id").val(shift.id); // Store shift ID for updating
          $("#name").val(shift.name);
          $("#start_time").val(shift.start_time);
          $("#end_time").val(shift.end_time);
  
          $("#saveShift").data("update-url", url); // Store update URL in button
          loadShifts(); // Refresh shift list
      });
  });
  
$("#saveShift").click(function () {
    let id = $("#shift_id").val();
    let url = id ? "/shifts/" + id : "/shifts"; // Change URL dynamically
    let method = id ? "PUT" : "POST"; // Change method dynamically

    $.ajax({
        url: url,
        type: method,
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"), // CSRF token
            name: $("#name").val(),
            start_time: $("#start_time").val(),
            end_time: $("#end_time").val(),
        },
        success: function () {
            $("#shiftForm")[0].reset(); // Reset form
            $("#shift_id").val(""); // Clear ID to switch back to POST
            loadShifts(); // Refresh shift list
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});

  
$(document).on("click", ".deleteShift", function () {
   let id = $(this).data("id");

   Swal.fire({
       title: "Êtes-vous sûr ?",
       text: "Cette action est irréversible !",
       icon: "warning",
       showCancelButton: true,
       confirmButtonColor: "#d33",
       cancelButtonColor: "#3085d6",
       confirmButtonText: "Oui, supprimer",
       cancelButtonText: "Annuler"
   }).then((result) => {
       if (result.isConfirmed) {
           $.ajax({
               url: "/shifts/" + id,
               type: "DELETE",
               success: function () {
                   Swal.fire("Supprimé!", "Le shift a été supprimé.", "success");
                   loadShifts();
               },
               error: function (xhr) {
                   console.log(xhr.responseText);
               }
           });
       }
   });
});

  $("#start_time, #end_time").on("change", function () {
      let startTime = $("#start_time").val();
      let endTime = $("#end_time").val();

      if (startTime && endTime) {
         // Format the name as [start_time - end_time]
         let formattedName = `[${startTime} - ${endTime}]`;
         $("#name").val(formattedName); // Set the generated name to the input field
      }
   });
});


document.addEventListener('DOMContentLoaded', function () {
   document.querySelectorAll('.btn-status').forEach(button => {
      button.addEventListener('click', function () {
         // Get the user ID from the button's data attributes
         let userId = this.getAttribute('data-user-id');
         let status = this.getAttribute('data-status');

         // Remove active class from both Présent and Absent buttons for this user
         let buttonsGroup = document.getElementById('attendance-buttons-' + userId);
         buttonsGroup.querySelectorAll('.btn-status').forEach(btn => btn.classList.remove('active'));

         // Add active class to the clicked button
         this.classList.add('active');

         // Update the hidden input with the selected status
         document.getElementById('status-' + userId).value = status;
      });
   });

   function updateSelectedUsers() {
      selectedUsersDiv.innerHTML = ''; // Clear existing badges

      selectedUserIds.forEach(userId => {
         const option = Array.from(userSelect.options).find(opt => opt.value == userId);
         if (option) {
            const userName = option.text;

            const badge = document.createElement('span');
            badge.className = 'badge bg-primary me-2 mb-2';
            badge.textContent = userName;

            const closeButton = document.createElement('button');
            closeButton.className = 'btn-close ms-1';
            closeButton.ariaLabel = 'Close';
            closeButton.type = 'button';
            closeButton.addEventListener('click', function () {
               // Deselect user in the select element
               option.selected = false;
               selectedUserIds.delete(option.value);
               updateSelectedUsers(); // Refresh badges
            });

            badge.appendChild(closeButton);
            selectedUsersDiv.appendChild(badge);
         }
      });
   }



   function updateClock() {
      const now = new Date();
      const day = String(now.getDate()).padStart(2, '0');
      const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
      const year = String(now.getFullYear()).slice(-2); // Get last two digits of year
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const timeString = `${hours}:${minutes}`;
      const dateString = `${day}-${month}-${year}`;
      document.querySelector('.time').textContent = timeString;
      document.querySelector('.date').textContent = dateString;
   }

   setInterval(updateClock, 1000);
   updateClock(); // Initial call to display the clock immediately

   const attendanceButtons = document.querySelectorAll('.btn-status');
   attendanceButtons.forEach(button => {
      button.addEventListener('click', function () {
         const userId = this.closest('tr').id.split('-')[2];
         const shiftId = this.closest('form').querySelector('input[name="shift_id"]').value;
         const attendanceStatus = this.dataset.status;

         // If the button is already active, deactivate it
         if (this.classList.contains('active')) {
            this.classList.remove('active');
            document.getElementById(`status_${userId}_${shiftId}`).value = ''; // Clear the hidden input value
         } else {
            // Remove active class from all buttons in the same row
            const rowButtons = document.querySelectorAll(`#attendance-buttons-${userId} .btn-status`);
            rowButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to the clicked button
            this.classList.add('active');

            // Set the hidden input value
            document.getElementById(`status_${userId}_${shiftId}`).value = attendanceStatus;
         }
      });
   });
   

   // card navs
   document.querySelectorAll(".shift").forEach((tab) => {
      tab.addEventListener("click", function (event) {
          event.preventDefault();
  
          let shiftId = this.getAttribute("data-shift-id");
          let shiftBody = document.getElementById(shiftId);
  
          if (!shiftBody) {
              console.error(`Element with ID '${shiftId}' not found.`);
              return; // Exit function if element is not found
          }
  
          document.querySelectorAll(".shift-card-body").forEach((body) => {
              body.classList.add("d-none"); // Hide all shift tables
          });
  
          shiftBody.classList.remove("d-none"); // Show selected shift table
  
          document.querySelectorAll(".shift").forEach((link) => {
              link.classList.remove("active");
          });
  
          this.classList.add("active");
      });
  });
  
});
