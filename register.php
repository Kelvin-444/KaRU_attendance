<?php 
include 'config.php'; 

if (isset($_POST['register'])) {
    $reg_number = mysqli_real_escape_string($conn, $_POST['reg_number']);
    $fullname   = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $course     = mysqli_real_escape_string($conn, $_POST['course']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if student already exists (pending or verified)
    $check = "SELECT * FROM students WHERE reg_number='$reg_number' OR email='$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        if ($existing['account_status'] == 'pending') {
            echo "<script>alert('Your registration is already pending approval. Please wait for verification.');</script>";
        } else {
            echo "<script>alert('Registration Number or Email already exists!');</script>";
        }
    } else {
        // FORCE role as student, status as pending, created_by as self
        $sql = "INSERT INTO students (reg_number, fullname, email, phone, course, password, role, account_status, created_by) 
                VALUES ('$reg_number', '$fullname', '$email', '$phone', '$course', '$password', 'student', 'pending', 'self')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration submitted! Your account is pending verification. You will be able to login once approved.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error occurred!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KaRU Attendance</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Karatina University</p>
    </div>

    <div class="nav">
        <a href="index.html">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Student Registration</h2>
            <p style="color: #e67e22; background: #fef9e7; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                ⚠️ <strong>Note:</strong> New registrations require verification from a lecturer before you can login.
            </p>
            <form action="register.php" method="POST" onsubmit="return validateRegistration()">
                <div class="form-group">
                    <label>Registration Number:</label><br>
                    <input type="text" id="reg_number" name="reg_number" placeholder="e.g. X123/4567Y/24" required>
                </div>
                <div class="form-group">
                    <label>Full Name:</label><br>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label>Email Address:</label><br>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label><br>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Course / Program:</label><br>
                    <input type="text" name="course" required>
                </div>
                <div class="form-group">
                    <label>Password:</label><br>
                    <input type="password" id="password" name="password" required>
                </div>;/;
                <button type="submit" name="register" class="btn">Register for Verification</button>
            </form>o
            <p style="text-align:center; margin-top:15px;">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
    <script src="js/validation.js"></script>
</body>
</html>