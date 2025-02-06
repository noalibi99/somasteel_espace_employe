/***Demandes page***/
$(document).ready(function () {
    var table = $('#demandes').DataTable({
        "language":{
            "url":"https://cdn.datatables.net/plug-ins/1.10.24/i18n/French.json",
        },
        searching: true,
        order: [[0, 'desc']] // Disable default search input
    });


});

let formConger = document.getElementById("form-conger");
let annulerFormConger = document.getElementById("annuler-form-conger");
let demandeConger = document.getElementById("demander-conger");

demandeConger.addEventListener('click', function () {
    if (formConger.classList.contains('fade-out')) {
        formConger.classList.remove('fade-out');
    }
    formConger.classList.remove("hidden");
    formConger.classList.add('fade-in'); // Apply the "hidden" class after the animation completes
})

annulerFormConger.addEventListener('click', function () {
    if (formConger.classList.contains('fade-in')) {
        formConger.classList.remove('fade-in');
    }
    formConger.classList.add('fade-out');
    setTimeout(function () {
        formConger.classList.add('hidden');
    }, 300);

});



//alerts errors

    // Function to show dynamic alert with error message
        


//desision

// $(document).ready(function(){
//     if (document.getElementById("decision-form")) {
//         let decisionForm = document.getElementById("decision-form");
//         let acceptButton = document.getElementById("accept");
//         let refusButton = document.getElementById("refus");

//         acceptButton.addEventListener('click')
//     }
// })

document.querySelectorAll('.accept-button').forEach(function (button) {
    button.addEventListener('click', function () {
        accept(this);
    });
});

document.querySelectorAll('.refus-button').forEach(function (button) {
    button.addEventListener('click', function () {
        refus(this);
    });
});
// document.querySelectorAll('.refus-button').forEach(function (button) {
//     button.addEventListener('click', function () {
//         accept(this);
//     });
// });

function accept(button) {
    let decisionForm = button.closest('.decision-form'); // Find the closest form element

    // Create a hidden input field
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'accepted';
    hiddenInput.value = '1';

    // Append the hidden input field to the form
    decisionForm.appendChild(hiddenInput);

    // Submit the form
    decisionForm.submit();
}
function refus(button) {
    let refusCard = document.getElementById('refus-card');

    // Remove the 'hidden' class and add fading in effect
    refusCard.classList.remove('hidden');
    refusCard.classList.add('fade-in');

    // Create hidden input fields for form submission
    

    // Add event listener to the 'confirme-refus' button
    document.getElementById('confirme-refus').addEventListener('click', function () {
        let decisionForm = button.closest('.decision-form'); // Find the closest form element
        let hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'refused';
        hiddenInput.value = 'Refusé';

        let raisonRefu = document.getElementById('raison-refus').value;
        let hiddenTextInput = document.createElement('input');
        hiddenTextInput.type = 'hidden';
        hiddenTextInput.name = 'raison_refus';
        hiddenTextInput.value = raisonRefu;

        // Append the hidden input fields to the form
        decisionForm.appendChild(hiddenInput);
        decisionForm.appendChild(hiddenTextInput);

        decisionForm.submit();
        // Add fading out effect when submitting the form
        refusCard.classList.remove('fade-in');
        refusCard.classList.add('fade-out');
        // Hide the 'refus-card' after fading out
        setTimeout(() => {
            refusCard.classList.add('hidden');
            refusCard.classList.remove('fade-out');
        }, 300); // Match the duration of fadeOut animation
    });

    // Add event listener to the 'annuler-refus' button
    document.getElementById('annuler-refus').addEventListener('click', function () {
        // Add fading out effect when canceling
        refusCard.classList.remove('fade-in');
        refusCard.classList.add('fade-out');
        // Hide the 'refus-card' after fading out
        setTimeout(() => {
            refusCard.classList.add('hidden');
            refusCard.classList.remove('fade-out');
        }, 300); // Match the duration of fadeOut animation
    });
}


// let refusButton = document.getElementById("refus");
// acceptButton.addEventListener('click', function () {
//     refus();
// });
