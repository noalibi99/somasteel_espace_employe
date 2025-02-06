// import '@fortawesome/fontawesome-free/js/all';
// import '@fortawesome/fontawesome-free/attribution';


/*LOGIN Page*/
let eyeInput = document.getElementById("see-password")

eyeInput.addEventListener('input', function () {
    let eye = document.getElementById("see-password-label")
    let inputPass = document.getElementById("password")
    // let inputPassC = document.getElementById("passwdc")
    if (eyeInput.checked) {
        eye.className = "see-password-label fas fa-eye pt-3";
        inputPass.type = "text";
    
        // inputPassC.type = "text";
    } else {
        eye.className = "see-password-label fas fa-eye-slash pt-3";
        inputPass.type = "password";
        // inputPassC.type = "password";
    }
})
