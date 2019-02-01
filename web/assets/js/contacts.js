
let addContactForm = document.getElementById("contacts--form");

if(addContactForm != null){

    let selectHelper = addContactForm.querySelector("#form_helper");
    let phone_input = addContactForm.querySelector("#form_phoneNumber");
    let phone_input_block = phone_input.parentElement;

    let toggleVisibility = function (val) {
        if(selectHelper.value === ""){
            phone_input_block.style.display = "block";
        } else {
            phone_input_block.style.display = "none";
        }
    }

    toggleVisibility();

    selectHelper.addEventListener('change', function () {
        toggleVisibility();
    });
}

