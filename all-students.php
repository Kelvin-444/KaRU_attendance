<?php 
include 'config.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_name = $_SESSION['fullname'];

// Handle filter
$filter = $_GET['filter'] ?? 'all';
$where_clause = "WHERE role = 'student'";
if ($filter === 'verified') {
    $where_clause .= " AND account_status = 'verified'";
} elseif ($filter === 'pending') {
    $where_clause .= " AND account_status = 'pending'";
} elseif ($filter === 'rejected') {
    $where_clause .= " AND account_status = 'rejected'";
}

// Get count for each status
$count_verified = $conn->query("SELECT COUNT(*) as c FROM students WHERE role='student' AND account_status='verified'")->fetch_assoc()['c'];
$count_pending = $conn->query("SELECT COUNT(*) as c FROM students WHERE role='student' AND account_status='pending'")->fetch_assoc()['c'];
$count_rejected = $conn->query("SELECT COUNT(*) as c FROM students WHERE role='student' AND account_status='rejected'")->fetch_assoc()['c'];
$count_all = $count_verified + $count_pending + $count_rejected;

$students = $conn->query("SELECT * FROM students $where_clause ORDER BY reg_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .filter-tab {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            background: #ecf0f1;
            color: #2c3e50;
            font-weight: bold;
            transition: all 0.3s;
        }
        .filter-tab:hover {
            background: #d5dbdb;
        }
        .filter-tab.active {
            background: #2980b9;
            color: white;
        }
        .filter-tab .count {
            background: rgba(255,255,255,0.3);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 5px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-verified { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }
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
        <a href="all-students.php">All Students</a>
        <a href="logout.php" style="color:red;">Logout</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>👥 All Students</h2>
            
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                    All <span class="count"><?= $count_all ?></span>
                </a>
                <a href="?filter=verified" class="filter-tab <?= $filter === 'verified' ? 'active' : '' ?>">
                    ✅ Verified <span class="count"><?= $count_verified ?></span>
                </a>
                <a href="?filter=pending" class="filter-tab <?= $filter === 'pending' ? 'active' : '' ?>">
                    ⏳ Pending <span class="count"><?= $count_pending ?></span>
                </a>
                <a href="?filter=rejected" class="filter-tab <?= $filter === 'rejected' ? 'active' : '' ?>">
                    ❌ Rejected <span class="count"><?= $count_rejected ?></span>
                </a>
            </div>

            <!-- Students Table -->
            <?php if ($students->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Reg Number</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Registered</th>
                </tr>
                <?php while($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['reg_number']) ?></strong></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td>
                        <span class="status-badge status-<?= $row['account_status'] ?>">
                            <?= ucfirst($row['account_status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['created_by']) ?></td>
                    <td><?= date('d M, Y', strtotime($row['reg_date'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
                <p style="text-align:center; padding:40px; color:#7f8c8d;">
                    No students found for this filter.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>