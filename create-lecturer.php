<?php
// create-lecturer.php
// ⚠️ RUN ONCE, THEN DELETE THIS FILE! ⚠️

include 'config.php';


// EDIT THESE VALUES FOR EACH LECTURER
$reg_number = 'LEC001';                    // Staff ID
$fullname   = 'Dr. Jane Muthoni';          // Full name
$email      = 'jane.muthoni@karu.ac.ke';   // Email
$phone      = '0700000000';                // Phone
$course     = 'Computer Science';          // Department
$password   = 'LecSecure@2024';            // Temporary password

// DO NOT EDIT BELOW THIS LINE
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO students (reg_number, fullname, email, phone, course, password, role, account_status, must_change_password, created_by) 
        VALUES ('$reg_number', '$fullname', '$email', '$phone', '$course', '$hashed_password', 'lecturer', 'verified', 1, 'system')";

if ($conn->query($sql)) {
    echo "<div style='font-family: Arial; max-width: 500px; margin: 50px auto; padding: 30px; background: #d4edda; border-radius: 10px;'>";
    echo "<h2>✅ Lecturer Account Created!</h2>";
    echo "<p><strong>Staff ID:</strong> $reg_number</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "<p><strong>Name:</strong> $fullname</p>";
    echo "<p style='color: red;'><strong>⚠️ LECTURER MUST CHANGE PASSWORD ON FIRST LOGIN</strong></p>";
    echo "<p style='color: red;'><strong>⚠️ DELETE THIS FILE NOW!</strong></p>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    echo "<p>Maybe this lecturer already exists?</p>";
}
?>