<?php
// create-students.php
// LECTURER USE ONLY - Create multiple student accounts at once

include 'config.php';
session_start();

// PROTECTION: Only lecturers can access this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$lecturer_name = $_SESSION['fullname'];
$message = '';
$messageType = '';

if (isset($_POST['bulk_create'])) {
    $students_text = $_POST['students_list'];
    $default_password = $_POST['default_password'];
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    // Parse the student list (format: REG_NUMBER,Full Name,Email,Phone per line)
    $lines = explode("\n", trim($students_text));
    $created = 0;
    $skipped = 0;
    $errors = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line))
            continue;

        $parts = str_getcsv($line); // Handles CSV format properly
        if (count($parts) < 4) {
            $errors[] = "Invalid format: $line";
            continue;
        }

        $reg_number = mysqli_real_escape_string($conn, trim($parts[0]));
        $fullname = mysqli_real_escape_string($conn, trim($parts[1]));
        $email = mysqli_real_escape_string($conn, trim($parts[2]));
        $phone = mysqli_real_escape_string($conn, trim($parts[3]));
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

        // Check if already exists
        $check = $conn->query("SELECT id FROM students WHERE reg_number='$reg_number' OR email='$email'");
        if ($check->num_rows > 0) {
            $skipped++;
            continue;
        }

        $sql = "INSERT INTO students (reg_number, fullname, email, phone, course, password, role, account_status, must_change_password, created_by) 
                VALUES ('$reg_number', '$fullname', '$email', '$phone', '$course', '$hashed_password', 'student', 'verified', 1, '$lecturer_name')";

        if ($conn->query($sql)) {
            $created++;
        } else {
            $errors[] = "Failed to create $reg_number: " . $conn->error;
        }
    }

    $message = "✅ Created: $created accounts | ⏭️ Skipped (already exist): $skipped";
    if (!empty($errors)) {
        $message .= "\nErrors: " . implode(", ", $errors);
        $messageType = 'warning';
    } else {
        $messageType = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student Accounts - KaRU</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        textarea {
            width: 100%;
            font-family: monospace;
        }

        .instructions {
            background: #eaf2f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2980b9;
        }

        .instructions code {
            background: #d5dbdb;
            padding: 2px 5px;
            border-radius: 3px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .warning-message {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
        }

        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
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
            <h2>📋 Bulk Create Student Accounts</h2>

            <?php if ($message): ?>
                <div class="<?= $messageType === 'success' ? 'success-message' : 'warning-message' ?>">
                    <?= nl2br(htmlspecialchars($message)) ?>
                </div>
            <?php endif; ?>

            <div class="instructions">
                <h3>📌 Instructions:</h3>
                <p>Paste student data below, <strong>one student per line</strong>, in CSV format:</p>
                <pre>REG_NUMBER,Full Name,Email,Phone</pre>
                <p><strong>Example:</strong></p>
                <pre>X123/4567Y/24,John Mwangi,john@example.com,0712345678
Y987/6543P/24,Jane Wanjiku,jane@example.com,0723456789
Z456/7890Q/24,Peter Kamau,peter@example.com,0734567890</pre>
                <p style="margin-top:10px;">💡 <strong>Tip:</strong> You can copy this directly from Excel or Google
                    Sheets (columns in order: Reg Number, Name, Email, Phone)</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label><strong>Course / Program for ALL students in this batch:</strong></label><br>
                    <input type="text" name="course" required placeholder="e.g. Bachelor of Computer Science">
                </div>

                <div class="form-group">
                    <label><strong>Default Password for ALL students:</strong></label><br>
                    <input type="text" name="default_password" required placeholder="e.g. Karu@2024"
                        value="<?= htmlspecialchars($_POST['default_password'] ?? 'Karu@2024') ?>">
                    <small style="color:#e67e22;">⚠️ Students will be forced to change this on first login</small>
                </div>

                <div class="form-group">
                    <label><strong>Student List (CSV format):</strong></label><br>
                    <textarea name="students_list" rows="15" required
                        placeholder="Paste student data here..."><?= htmlspecialchars($_POST['students_list'] ?? '') ?></textarea>
                </div>

                <button type="submit" name="bulk_create" class="btn" style="font-size:18px; padding:15px 40px;">
                    ✅ Create Student Accounts
                </button>
            </form>
        </div>
    </div>
</body>

</html>