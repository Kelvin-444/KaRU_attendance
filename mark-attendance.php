<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];
$today = date('Y-m-d');

// Check if already signed today
$check = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND class_date = '$today'");
$already_marked = $check->num_rows > 0;

if (isset($_POST['sign_attendance'])) {
    if ($already_marked) {
        echo "<script>alert('You have already signed attendance for today!');</script>";
    } else {
        $sql = "INSERT INTO attendance (student_id, class_date, status) 
                VALUES ($student_id, '$today', 'Present')";
        
        if ($conn->query($sql)) {
            echo "<script>alert('Attendance signed successfully for today!'); window.location='mark-attendance.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Attendance - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($fullname) ?> (Student)</p>
    </div>

    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="mark-attendance.php">Sign Attendance</a>
        <a href="view-attendance.php">My Record</a>
        <a href="logout.php" style="color:red;">Logout</a>
    </div>

    <div class="container">
        <div class="card" style="text-align:center; padding:60px 20px;">
            <h2>Sign Today's Attendance</h2>
            <p><strong>Date:</strong> <?= date('d M, Y') ?></p>
            <p><strong>Student:</strong> <?= htmlspecialchars($fullname) ?></p>

            <?php if ($already_marked): ?>
                <h3 style="color:green;">✅ You have already signed in for today.</h3>
            <?php else: ?>
                <form method="POST">
                    <button type="submit" name="sign_attendance" class="btn" style="font-size:22px; padding:20px 60px;">
                        ✅ SIGN ATTENDANCE (PRESENT)
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>