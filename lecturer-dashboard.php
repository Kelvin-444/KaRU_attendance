<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
$today = date('Y-m-d');

// Today's Attendance Summary
$total_students = $conn->query("SELECT COUNT(*) as total FROM students WHERE role='student' AND account_status='verified'")->fetch_assoc()['total'];
$signed_today = $conn->query("SELECT COUNT(DISTINCT student_id) as signed FROM attendance WHERE class_date = '$today'")->fetch_assoc()['signed'];

// Pending approvals count
$pending_count = $conn->query("SELECT COUNT(*) as count FROM students WHERE account_status = 'pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .badge {
            background: #e74c3c;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .stat-card {
            text-align: center;
            padding: 25px;
            border-radius: 10px;
            color: white;
        }
        .stat-card.blue { background: linear-gradient(135deg, #2980b9, #3498db); }
        .stat-card.green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .stat-card.orange { background: linear-gradient(135deg, #e67e22, #f39c12); }
        .stat-card.red { background: linear-gradient(135deg, #c0392b, #e74c3c); }
        .stat-card h2 { font-size: 48px; margin: 10px 0; }
        .stat-card p { font-size: 16px; opacity: 0.9; }
        .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 30px; }
        .quick-action-btn {
            display: block;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .quick-action-btn:hover { transform: translateY(-3px); }
        .btn-blue { background: #2980b9; }
        .btn-green { background: #27ae60; }
        .btn-orange { background: #e67e22; }
        .btn-purple { background: #8e44ad; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KaRU Attendance Tracker</h1>
        <p>Welcome, <?= htmlspecialchars($fullname) ?> (Lecturer)</p>
    </div>

    <div class="nav">
        <a href="lecturer-dashboard.php">Dashboard</a>
        <a href="attendance-report.php">Today's Report</a>
        <a href="create-students.php">Create Students</a>
        <a href="pending-approvals.php">
            Pending Approvals 
            <?php if ($pending_count > 0): ?>
                <span class="badge"><?= $pending_count ?> new</span>
            <?php endif; ?>
        </a>
        <a href="logout.php" style="color:red;">Logout</a>
    </div>

    <div class="container">
        <!-- Alert for pending approvals -->
        <?php if ($pending_count > 0): ?>
        <div style="background: #fff3cd; border: 2px solid #f39c12; border-radius: 10px; padding: 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="color: #856404; margin: 0;">⚠️ Pending Student Approvals</h3>
                <p style="margin: 5px 0 0 0;">There are <strong><?= $pending_count ?> student(s)</strong> waiting for verification.</p>
            </div>
            <a href="pending-approvals.php" class="btn" style="background: #f39c12; font-size: 18px; white-space: nowrap;">
                Review Now →
            </a>
        </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="dashboard-grid">
            <div class="stat-card blue">
                <p>Total Verified Students</p>
                <h2><?= $total_students ?></h2>
            </div>
            <div class="stat-card green">
                <p>Signed In Today</p>
                <h2><?= $signed_today ?></h2>
            </div>
            <div class="stat-card orange">
                <p>Attendance Rate Today</p>
                <h2><?= $total_students > 0 ? round(($signed_today / $total_students) * 100, 1) : 0 ?>%</h2>
            </div>
            <div class="stat-card <?= $pending_count > 0 ? 'red' : 'green' ?>">
                <p>Pending Approvals</p>
                <h2><?= $pending_count ?></h2>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="attendance-report.php" class="quick-action-btn btn-blue">
                    📋 View Today's Report
                </a>
                <a href="create-students.php" class="quick-action-btn btn-green">
                    ➕ Create Student Accounts
                </a>
                <a href="pending-approvals.php" class="quick-action-btn <?= $pending_count > 0 ? 'btn-orange' : 'btn-purple' ?>">
                    <?php if ($pending_count > 0): ?>
                        🔔 Review Pending (<?= $pending_count ?>)
                    <?php else: ?>
                        ✔️ All Approved
                    <?php endif; ?>
                </a>
                <a href="all-students.php" class="quick-action-btn btn-purple">
                    👥 View All Students
                </a>
            </div>
        </div>
    </div>
</body>
</html>