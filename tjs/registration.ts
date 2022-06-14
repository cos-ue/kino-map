/**
 * checks if mailadress is already
 */
function checkMailAdress(): void {
    var mailfield = document.getElementById('emailAddUser') as HTMLInputElement;
    var mail = mailfield.value;
    var json = {
        type: "cma",
        mail: mail
    }
    var result = sendApiRequest(json, false);
    if (result.data) {
        var current = mailfield.getAttribute('class');
        current += " border-danger bg-danger";
        mailfield.setAttribute('class', current);
        mailfield.setAttribute('data-toggle', "tooltip");
        mailfield.setAttribute('title', "Diese E-Mailadresse wird bereits verwendet.")
    } else {
        mailfield.setAttribute('class', 'form-control textinput');
        mailfield.removeAttribute('data-toggle');
        mailfield.removeAttribute('title');
        var buttonSubmit = document.getElementById('submitRegistration') as HTMLButtonElement;
        buttonSubmit.click();
    }
}