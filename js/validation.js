// js/validation.js
function validateRegistration() {
    var regNo = document.getElementById("reg_number").value.trim();
    var fullName = document.getElementById("fullname").value.trim();
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var password = document.getElementById("password").value.trim();

    // Check if fields are blank
    if (regNo === "" || fullName === "" || email === "" || phone === "" || password === "") {
        alert("All fields are required!");
        return false;
    }

    // Registration Number format (e.g., S123/001X/24 or Y987/6543P/21)
    // Letter, numbers, slash, numbers, optional letter, slash, two-digit year
    if (!/^[A-Z]\d{3}\/\d{4}[A-Z]?\/\d{2}$/.test(regNo)) {
        alert("Invalid Registration Number format! Example: S123/001X/24");
        return false;
    }

    // Email validation
    if (!email.includes("@") || !email.includes(".")) {
        alert("Please enter a valid email address!");
        return false;
    }

    // Phone number validation (Kenyan format: 07XX or 01XX, 10 digits)
    if (!/^0[17]\d{8}$/.test(phone)) {
        alert("Phone number must be a valid Kenyan number (e.g., 0712345678)!");
        return false;
    }

    // Password length
    if (password.length < 6) {
        alert("Password must be at least 6 characters long!");
        return false;
    }

    return true;
}