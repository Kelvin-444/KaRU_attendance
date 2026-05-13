<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

$sql = "SELECT class_date, status FROM attendance WHERE student_id = $student_id ORDER BY class_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - KaRU</title>
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
            <h2>My Attendance Record</h2>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['class_date'] ?></td>
                        <td class="<?= $row['status']=='Present' ? 'success' : 'danger' ?>">
                            <strong><?= $row['status'] ?></strong>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align:center; padding:40px;">No records yet. Sign attendance first.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>