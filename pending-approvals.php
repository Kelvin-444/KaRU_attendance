<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_name = $_SESSION['fullname'];

// Handle approval/rejection
if (isset($_POST['action']) && isset($_POST['student_id'])) {
    $student_id = (int) $_POST['student_id'];
    $action = $_POST['action']; // 'verified' or 'rejected'

    // Get student info for the alert message
    $student_info = $conn->query("SELECT fullname, reg_number FROM students WHERE id = $student_id")->fetch_assoc();

    if ($conn->query("UPDATE students SET account_status = '$action' WHERE id = $student_id AND account_status = 'pending'")) {
        $status_text = ($action == 'verified') ? 'approved' : 'rejected';
        echo "<script>alert('Student " . htmlspecialchars($student_info['fullname']) . " (" . $student_info['reg_number'] . ") has been $status_text!'); window.location='pending-approvals.php';</script>";
    }
}

// Get pending students
$pending = $conn->query("SELECT * FROM students WHERE account_status = 'pending' ORDER BY reg_date DESC");
$pending_count = $pending->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .btn-approve {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-reject {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 5px;
        }

        .btn-approve:hover {
            background: #219a52;
        }

        .btn-reject:hover {
            background: #c0392b;
        }

        .warning-banner {
            background: #fff3cd;
            border: 1px solid #f39c12;
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2980b9;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($lecturer_name) ?> (Lecturer)</p>
    </div>

    <div class="nav">
        <a href="lecturer-dashboard.php">Dashboard</a>
        <a href="attendance-report.php">Today's Report</a>
        <a href="create-students.php">Create Students</a>
        <a href="pending-approvals.php">Pending Approvals</a>
        <a href="logout.php" style="color:red;">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>📋 Pending Student Approvals</h2>

            <?php if ($pending_count > 0): ?>
                <div class="warning-banner">
                    <strong><?= $pending_count ?> student(s)</strong> waiting for verification.
                    Please confirm with administration records before approving.
                </div>
            <?php endif; ?>

            <?php if ($pending_count > 0): ?>
                <table>
                    <tr>
                        <th>#</th>
                        <th>Reg Number</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                    <?php $count = 1; ?>
                    <?php while ($row = $pending->fetch_assoc()): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><strong><?= htmlspecialchars($row['reg_number']) ?></strong></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= date('d M, Y', strtotime($row['reg_date'])) ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="student_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="action" value="verified" class="btn-approve"
                                        onclick="return confirm('Approve <?= htmlspecialchars(addslashes($row['fullname'])) ?> (<?= htmlspecialchars($row['reg_number']) ?>)?\n\nThis will allow them to login.')">
                                        ✔️ Approve
                                    </button>
                                    <button type="submit" name="action" value="rejected" class="btn-reject"
                                        onclick="return confirm('Reject <?= htmlspecialchars(addslashes($row['fullname'])) ?> (<?= htmlspecialchars($row['reg_number']) ?>)?\n\nThey will NOT be able to login.')">
                                        ❌ Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">✔️</div>
                    <h3>No Pending Approvals</h3>
                    <p>All student registrations have been processed. New self-registrations will appear here.</p>
                    <a href="lecturer-dashboard.php" class="back-link"> < Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>