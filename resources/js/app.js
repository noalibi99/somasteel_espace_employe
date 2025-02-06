// import './bootstrap';
// import '/node_modules/jquery/src/jquery'

import $ from 'jquery';

// Expose jQuery globally
window.$ = $;
window.jQuery = $;

import '/node_modules/bootstrap/dist/js/bootstrap.bundle.min'


// import 'jquery-ui/ui/widgets/autocomplete';
// /bootstrap.bundle.min.
//import { createPopper } from '@popperjs/core';

// import '/node_modules/bootstrap/dist/js/bootstrap.bundle'
// import '/node_modules/bootstrap/dist/js/bootstrap.min'
// import '/node_modules/bootstrap/js/dist/popover'
// import '/node_modules/@popperjs/core/lib/createPopper'

import '/node_modules/datatables.net-dt/js/dataTables.dataTables'
import '/node_modules/datatables.net/js/dataTables'
// import '/node_modules/file-saver/dist/FileSaver.min.js';
import '/node_modules/xlsx/dist/xlsx.core.min.js';


$('#offcanvasNavbar').on('show.bs.offcanvas', function () {
    $('.menu-label svg').addClass('open');
});

$('#offcanvasNavbar').on('hide.bs.offcanvas', function () {
    $('.menu-label svg').removeClass('open');
});

var timeoutId;

function showErrorAlert(message) {
    $('#errorMessage').html(message);

    $('#dynamicErrorAlert').show().addClass('slideInFromRight');

    // Clear previous timeout if exists
    clearTimeout(timeoutId);

    // Set a timeout to hide the alert after 4 seconds
    timeoutId = setTimeout(function () {
        $('#dynamicErrorAlert').removeClass('slideInFromRight');
        $('#dynamicErrorAlert').addClass('slideOutToRight');
        setTimeout(function () {
            $('#dynamicErrorAlert').hide().removeClass('slideOutToRight');
        }, 500); // Adjust duration as needed
    }, 4000); // Adjust duration as needed
}

function showSuccessAlert(message) {
    $('#successMessage').text(message);
    $('#dynamicSuccessAlert').show().addClass('slideInFromRight');

    // Set a timeout to hide the success alert after 3 seconds
    setTimeout(function () {
        $('#dynamicSuccessAlert').removeClass('slideInFromRight').addClass('slideOutToRight');
        setTimeout(function () {
            $('#dynamicSuccessAlert').hide().removeClass('slideOutToRight');
        }, 500); // Adjust duration as needed
    }, 3000); // Adjust duration as needed
}

// Check for errors and display dynamic alert if present
$(document).ready(function () {
	$('.pop-refus').hover(function () {
        var content = $(this).data('bs-content');
        var popover = $('<div class="popover-content">' + content + '</div>');
        popover.appendTo('body').css({
            top: $(this).offset().top - popover.outerHeight(),
            left: $(this).offset().left
        }).show();
    }, function () {
        $('.popover-content').remove();
    });
    
    let errorSpan = document.getElementById("errorMessage");
    let successSpan = document.getElementById("successMessage");

    if (errorSpan.textContent.trim()) {
        showErrorAlert(errorSpan.textContent);
    }
    if (successSpan.textContent.trim()) {
        showSuccessAlert(successSpan.textContent);
    }

});
