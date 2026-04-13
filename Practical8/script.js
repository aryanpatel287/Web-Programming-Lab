var registrationForm = document.getElementById("registrationForm");
var formMessage = document.getElementById("formMessage");

var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
var phoneRegex = /^[6-9][0-9]{9}$/;
var enrollRegex = /^[0-9]{12}$/;

function showError(errorId, message) {
    document.getElementById(errorId).textContent = message;
}

function validateTextField(inputId, errorId, message) {
    var inputValue = document.getElementById(inputId).value.trim();

    if (inputValue === "") {
        showError(errorId, message);
        return false;
    }

    showError(errorId, "");
    return true;
}

function validatePattern(inputId, errorId, regex, emptyMessage, invalidMessage) {
    var inputValue = document.getElementById(inputId).value.trim();

    if (inputValue === "") {
        showError(errorId, emptyMessage);
        return false;
    }

    if (!regex.test(inputValue)) {
        showError(errorId, invalidMessage);
        return false;
    }

    showError(errorId, "");
    return true;
}

function validateRadioGroup(groupName, errorId, message) {
    var selectedOption = document.querySelector("input[name='" + groupName + "']:checked");

    if (!selectedOption) {
        showError(errorId, message);
        return false;
    }

    showError(errorId, "");
    return true;
}

function validateCheckbox(inputId, errorId, message) {
    var checkbox = document.getElementById(inputId);

    if (!checkbox.checked) {
        showError(errorId, message);
        return false;
    }

    showError(errorId, "");
    return true;
}

function validateForm(event) {
    event.preventDefault();

    var isValid = true;

    isValid = validateTextField("fullname", "fullnameError", "Please enter your full name.") && isValid;
    isValid = validatePattern("email", "emailError", emailRegex, "Please enter your email address.", "Please enter a valid email address.") && isValid;
    isValid = validatePattern("password", "passwordError", passwordRegex, "Please enter a password.", "Password must contain uppercase, lowercase, number, special character (@$!%*?&), and minimum 8 characters.") && isValid;
    isValid = validatePattern("phone", "phoneError", phoneRegex, "Please enter your mobile number.", "Mobile number must be 10 digits and start with 6, 7, 8, or 9.") && isValid;
    isValid = validateTextField("dob", "dobError", "Please select your date of birth.") && isValid;
    isValid = validateRadioGroup("gender", "genderError", "Please select your gender.") && isValid;
    isValid = validatePattern("enroll", "enrollError", enrollRegex, "Please enter your enrollment number.", "Enrollment number must contain exactly 12 digits.") && isValid;
    isValid = validateTextField("branch", "branchError", "Please select your branch.") && isValid;
    isValid = validateTextField("semester", "semesterError", "Please enter your semester.") && isValid;
    isValid = validateTextField("event", "eventError", "Please select an event.") && isValid;
    isValid = validateTextField("heardFrom", "heardFromError", "Please select how you heard about this event.") && isValid;
    isValid = validateCheckbox("terms", "termsError", "Please accept the terms and conditions.") && isValid;

    if (isValid) {
        formMessage.textContent = "Registration submitted successfully.";
        formMessage.className = "form-message success";
        registrationForm.reset();
    } else {
        formMessage.textContent = "Please correct the errors and submit again.";
        formMessage.className = "form-message error";
    }
}

function clearMessages() {
    var errorMessages = document.getElementsByClassName("error-message");

    for (var i = 0; i < errorMessages.length; i++) {
        errorMessages[i].textContent = "";
    }

    formMessage.textContent = "";
    formMessage.className = "form-message";
}

registrationForm.addEventListener("submit", validateForm);
registrationForm.addEventListener("reset", clearMessages);
