<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$today = date('Y-m-d');

$sql = "SELECT s.reg_number, s.fullname, s.course, a.status 
        FROM students s 
        LEFT JOIN attendance a ON s.id = a.student_id AND a.class_date = '$today'
        WHERE s.role = 'student'
        ORDER BY s.fullname";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Attendance Report - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Lecturer View</p>
    </div>

    <div class="nav">
        <a href="lecturer-dashboard.php">Dashboard</a>
        <a href="attendance-report.php">Today's Report</a>
        <a href="logout.php" style="color:red;">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>Today's Attendance Report (<?= date('d M, Y') ?>)</h2>
            
            <table>
                <tr>
                    <th>Reg Number</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Status</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['reg_number'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td class="<?= $row['status']=='Present' ? 'success' : 'danger' ?>">
                        <strong><?= $row['status'] ?? 'Not Signed' ?></strong>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>