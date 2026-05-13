<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Attendance Percentage
$total = $conn->query("SELECT COUNT(*) as t FROM attendance WHERE student_id = $student_id")->fetch_assoc()['t'];
$present = $conn->query("SELECT COUNT(*) as p FROM attendance WHERE student_id = $student_id AND status='Present'")->fetch_assoc()['p'];
$percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - KaRU</title>
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
        <div class="card">
            <h2>Student Dashboard</h2>
            <p><strong>Reg Number:</strong> <?= $_SESSION['reg_number'] ?></p>Tables
            
            <h3>Attendance Summary</h3>
            <p><strong>Total Classes:</strong> <?= $total ?> | 
               <strong>Present:</strong> <?= $present ?> | 
               <strong>Percentage:</strong> 
               <span class="<?= $percentage >= 75 ? 'success' : 'danger' ?>"><?= $percentage ?>%</span>
            </p>

            <div style="text-align:center; margin:40px 0;">
                <a href="mark-attendance.php" class="btn">Sign Today's Attendance</a>
                <a href="view-attendance.php" class="btn">View Full Record</a>
            </div>
        </div>
    </div>
</body>
</html>