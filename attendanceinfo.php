<?php
// attendanceinfo.php
session_start();
include 'db_connect.php'; // Your DB connection file

// Initialize filters
$date_filter = '';
$subject_filter = '';
$where = [];
$params = [];
$types = "";

// Handle filters
if(isset($_GET['filter'])){
    if(!empty($_GET['date'])){
        $date_filter = $_GET['date'];
        $where[] = "date=?";
        $params[] = $date_filter;
        $types .= "s";
    }
    if(!empty($_GET['subject_group'])){
        $subject_filter = $_GET['subject_group'];
        $where[] = "subject_group=?";
        $params[] = $subject_filter;
        $types .= "s";
    }
}

// Build query
$sql = "SELECT a.*, s.name, s.contact_number 
        FROM attendance a 
        JOIN student_register s ON a.student_id = s.student_id";

if(!empty($where)){
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY a.date DESC, a.time DESC";

$stmt = $conn->prepare($sql);

// Bind params if any
if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch all distinct subject groups for filter dropdown
$subject_groups = [];
$sub_res = $conn->query("SELECT DISTINCT subject_group FROM attendance");
while($row = $sub_res->fetch_assoc()){
    $subject_groups[] = $row['subject_group'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Info</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        input, select, button { padding: 5px; margin-right: 10px; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>
<h2>Attendance Info</h2>

<!-- Filter Form -->
<form method="GET" action="">
    <label>Date:</label>
    <input type="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">

    <label>Subject Group:</label>
    <select name="subject_group">
        <option value="">--All--</option>
        <?php foreach($subject_groups as $sg): ?>
            <option value="<?= htmlspecialchars($sg) ?>" <?= ($sg==$subject_filter) ? "selected" : "" ?>><?= htmlspecialchars($sg) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="filter">Filter</button>
</form>

<!-- Attendance Table -->
<table>
    <tr>
        
        <th>Student ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Subject Group</th>
        <th>Status</th>
        <th>Date</th>
        <th>Time</th>
    </tr>
    <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['contact_number']) ?></td>
                <td><?= htmlspecialchars($row['subject_group']) ?></td>
                <td><?= ($row['status']=='true') ? 'Present' : 'Absent' ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['time'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">No records found.</td></tr>
    <?php endif; ?>
</table>
</body>
</html>
