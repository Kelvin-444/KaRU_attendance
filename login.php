<?php 
include 'config.php'; 
session_start();

if (isset($_POST['login'])) {
    $reg_number = mysqli_real_escape_string($conn, $_POST['reg_number']);
    $password   = $_POST['password'];

    $sql = "SELECT * FROM students WHERE reg_number = '$reg_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Check account status first
        if ($row['account_status'] === 'pending') {
            echo "<script>alert('Your account is pending verification. Please wait for a lecturer to approve it.');</script>";
        } 
        elseif ($row['account_status'] === 'rejected') {
            echo "<script>alert('Your registration has been rejected. Please contact your lecturer.');</script>";
        }
        elseif (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['reg_number'] = $row['reg_number'];
            $_SESSION['role'] = $row['role'];

            // Check if password change is required
            if ($row['must_change_password'] == 1) {
                header("Location: change-password.php");
                exit();
            }

            if ($row['role'] == 'lecturer') {
                header("Location: lecturer-dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect Password!');</script>";
        }
    } else {
        echo "<script>alert('Registration Number not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KaRU Attendance</title>
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
            <h2>Login to System</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Registration Number / Staff ID:</label><br>
                    <input type="text" name="reg_number" required>
                </div>
                <div class="form-group">
                    <label>Password:</label><br>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>