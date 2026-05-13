<?php
include 'config.php';
session_start();

// Must be logged in to change password
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current hash
    $result = $conn->query("SELECT password FROM students WHERE id = $user_id");
    $row = $result->fetch_assoc();
    
    if (!password_verify($current_password, $row['password'])) {
        $message = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters!";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE students SET password = '$new_hash', must_change_password = 0 WHERE id = $user_id");
        
        echo "<script>alert('Password changed successfully! You will now be redirected.'); 
              window.location='" . ($_SESSION['role'] == 'lecturer' ? 'lecturer-dashboard.php' : 'dashboard.php') . "';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Security - Change Your Password</p>
    </div>

    <div class="container">
        <div class="card">
            <h2>🔒 Change Your Password</h2>
            <p style="color: #e67e22;">⚠️ You must change your default password before continuing.</p>
            
            <?php if ($message): ?>
                <p style="color: red; background: #fce4e4; padding: 10px; border-radius: 5px;"><?= $message ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Current Password:</label><br>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password:</label><br>
                    <input type="password" name="new_password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm New Password:</label><br>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>