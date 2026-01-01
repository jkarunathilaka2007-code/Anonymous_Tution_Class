<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Initialize admin variable
$admin = $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{
    --primary:#4dabf7;
    --secondary:#1f1f1f;
    --bg:#0f111a;
    --card:#1f1f2e;
    --text:#f1f1f1;
    --hover:#00d4ff;
}
/* Reset */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body, html{height:100%; overflow-x:hidden;}
/* Gradient animated background */
body{
    background: linear-gradient(135deg, #0f111a, #1a1f3a, #0f111a);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    color: var(--text);
    position: relative;
}
@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}
/* Floating shapes */
.shape{
    position:absolute;
    border-radius:50%;
    opacity:0.15;
    animation: floatShape 20s linear infinite;
}
.shape1{width:150px;height:150px;background:#4dabf7;top:10%;left:5%;}
.shape2{width:100px;height:100px;background:#00d4ff;top:50%;left:80%;}
.shape3{width:200px;height:200px;background:#ff6b81;top:80%;left:20%;}
@keyframes floatShape{
    0%{transform: translateY(0px) translateX(0px);}
    50%{transform: translateY(-30px) translateX(20px);}
    100%{transform: translateY(0px) translateX(0px);}
}
/* Container */
.container{display:flex;min-height:100vh;position:relative; z-index:1;}
/* Sidebar */
.sidebar{
    width:240px;background:rgba(31,31,31,0.8);backdrop-filter:blur(10px);
    padding:25px 15px;display:flex;flex-direction:column;transition:0.3s;border-radius:0 20px 20px 0;
}
.sidebar h2{color:var(--primary);text-align:center;margin-bottom:30px;}
.sidebar a{
    color:var(--text);padding:12px 15px;margin-bottom:12px;
    border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:500;
    transition:0.3s;background:rgba(0,0,0,0.2);
}
.sidebar a:hover{background:var(--hover);color:#000;transform:scale(1.05);}
/* Content */
.content{flex:1;padding:30px;overflow-y:auto;position:relative; z-index:1;}
h1,h2,h3{color:var(--primary);margin-bottom:15px;}
/* Glassmorphic cards */
.card{
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border-radius:20px;
    padding:25px;
    margin-bottom:20px;
    box-shadow:0 8px 32px rgba(0,212,255,0.2);
    transition:0.3s;
    opacity:0;
    animation: fadeIn 1s forwards;
}
.card:hover{transform:translateY(-5px);box-shadow:0 0 30px rgba(0,212,255,0.3);}
@keyframes fadeIn{to{opacity:1;}}
/* Table */
table{width:100%;border-collapse:collapse;color:#fff;margin-top:20px;}
table th, table td{padding:10px;text-align:left;border-bottom:1px solid #444;}
table th{background:rgba(31,31,63,0.8);}
table tr:hover{background:rgba(0,212,255,0.1);}
/* Filter form */
form input, form select, form button{
    padding:8px 10px;
    margin-right:10px;
    border-radius:8px;
    border:none;
    outline:none;
}
form button{
    background:var(--primary);
    color:#000;
    cursor:pointer;
    transition:0.3s;
}
form button:hover{background:var(--hover);}
</style>
</head>
<body>

<!-- Floating shapes -->
<div class="shape shape1"></div>
<div class="shape shape2"></div>
<div class="shape shape3"></div>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="?page=dashboard"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=students"><i class="fa fa-user-graduate"></i> Students</a>
        <a href="?page=teachers"><i class="fa fa-chalkboard-teacher"></i> Teachers</a>
        <a href="?page=employers"><i class="fa fa-briefcase"></i> Employers</a>
        <a href="register.php"><i class="fa fa-check-circle"></i> Registration</a>
        <a href="attendance.php"><i class="fa fa-check-circle"></i> Mark Attendance</a>
        <a href="?page=attendance_info"><i class="fa fa-calendar-check"></i> Attendance Info</a>
        <a href="classfees.php"><i class="fa fa-money-bill"></i> Class Fees</a>
        <a href="?page=classfees_info"><i class="fa fa-money-bill"></i> Class Fees Info</a>
        <a href="teacherfee.php"><i class="fa fa-money-bill"></i> Mark Teacher Fee</a>
        <a href="employerfee.php"><i class="fa fa-money-bill"></i> Mark Employer Fee</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
<?php
$page = $_GET['page'] ?? 'dashboard';

if($page=='dashboard'):
    ?>
    <h1>Welcome Admin!</h1>
    <p>This is your admin dashboard. You can manage students, teachers, and employers from the menu.</p>
    <div class="card">
        <h3>Total Students</h3>
        <?php
        $res = $conn->query("SELECT COUNT(*) as total FROM student_register");
        $row = $res->fetch_assoc();
        echo "<p style='font-size:24px;'>".($row['total'] ?? 0)."</p>";
        ?>
    </div>
    <div class="card">
        <h3>Total Teachers</h3>
        <?php
        $res = $conn->query("SELECT COUNT(*) as total FROM teacher_register");
        $row = $res->fetch_assoc();
        echo "<p style='font-size:24px;'>".($row['total'] ?? 0)."</p>";
        ?>
    </div>
    <div class="card">
        <h3>Total Employers</h3>
        <?php
        $res = $conn->query("SELECT COUNT(*) as total FROM employer_register");
        $row = $res->fetch_assoc();
        echo "<p style='font-size:24px;'>".($row['total'] ?? 0)."</p>";
        ?>
    </div>

<?php
elseif($page=='students'):
    $res = $conn->query("SELECT * FROM student_register");
    if(!$res){ die("Query failed: ".$conn->error); }
    echo "<h1>All Students</h1>";
    echo "<table><tr><th>ID</th><th>Name</th><th>Contact</th><th>School</th></tr>";
    while($row = $res->fetch_assoc()){
        echo "<tr><td>".htmlspecialchars($row['student_id'])."</td><td>".htmlspecialchars($row['name'])."</td><td>".htmlspecialchars($row['contact_number'])."</td><td>".htmlspecialchars($row['school'])."</td></tr>";
    }
    echo "</table>";

elseif($page=='teachers'):
    $res = $conn->query("SELECT * FROM teacher_register");
    if(!$res){ die("Query failed: ".$conn->error); }
    echo "<h1>All Teachers</h1>";
    echo "<table><tr><th>ID</th><th>Name</th><th>Contact</th><th>Subject</th></tr>";
    while($row = $res->fetch_assoc()){
        echo "<tr><td>".htmlspecialchars($row['teacher_id'])."</td><td>".htmlspecialchars($row['name'])."</td><td>".htmlspecialchars($row['contact_number'])."</td><td>".htmlspecialchars($row['teach_subject'])."</td></tr>";
    }
    echo "</table>";

elseif($page=='employers'):
    $res = $conn->query("SELECT * FROM employer_register");
    if(!$res){ die("Query failed: ".$conn->error); }
    echo "<h1>All Employers</h1>";
    echo "<table><tr><th>ID</th><th>Name</th><th>Contact</th><th>Position</th></tr>";
    while($row = $res->fetch_assoc()){
        echo "<tr><td>".htmlspecialchars($row['employer_id'])."</td><td>".htmlspecialchars($row['name'])."</td><td>".htmlspecialchars($row['contact_number'])."</td><td>".htmlspecialchars($row['position'])."</td></tr>";
    }
    echo "</table>";

elseif($page=='attendance_info'):
    // Attendance Filters
    $dateFilter = $_GET['filter_date'] ?? '';
    $subjectFilter = $_GET['filter_subject'] ?? '';
    $groupFilter = $_GET['filter_group'] ?? '';
    $search = $_GET['search'] ?? '';

    $query = "SELECT a.student_id, s.name AS student_name, t.teach_subject AS subject_name, a.subject_group, a.status, a.date, a.time
              FROM attendance a
              JOIN student_register s ON a.student_id = s.student_id
              JOIN teachers_subjects_group tg ON a.subject_group = tg.subject_group
              JOIN teacher_register t ON tg.teacher_id = t.teacher_id
              WHERE 1=1";

    if($dateFilter) $query .= " AND a.date = '".$conn->real_escape_string($dateFilter)."'";
    if($subjectFilter) $query .= " AND t.teach_subject = '".$conn->real_escape_string($subjectFilter)."'";
    if($groupFilter) $query .= " AND a.subject_group = '".$conn->real_escape_string($groupFilter)."'";
    if($search) $query .= " AND (s.name LIKE '%".$conn->real_escape_string($search)."%' OR t.teach_subject LIKE '%".$conn->real_escape_string($search)."%')";

    $query .= " ORDER BY a.date DESC, a.time DESC";
    $res = $conn->query($query);
    if(!$res){ die("Query failed: ".$conn->error); }

    ?>
    <h1>Attendance Info</h1>
    <form method="get" style="margin-bottom:20px;">
        <input type="hidden" name="page" value="attendance_info">
        <input type="date" name="filter_date" value="<?=htmlspecialchars($dateFilter)?>">
        <select name="filter_subject">
            <option value="">All Subjects</option>
            <?php
            $subjects = $conn->query("SELECT DISTINCT teach_subject FROM teacher_register ORDER BY teach_subject ASC");
            while($sub = $subjects->fetch_assoc()){
                $selected = ($subjectFilter == $sub['teach_subject']) ? "selected" : "";
                echo "<option value='".htmlspecialchars($sub['teach_subject'])."' $selected>".htmlspecialchars($sub['teach_subject'])."</option>";
            }
            ?>
        </select>
        <select name="filter_group">
            <option value="">All Subject Groups</option>
            <?php
            $groups = $conn->query("SELECT DISTINCT subject_group FROM teachers_subjects_group ORDER BY subject_group ASC");
            while($grp = $groups->fetch_assoc()){
                $selected = ($groupFilter == $grp['subject_group']) ? "selected" : "";
                echo "<option value='".htmlspecialchars($grp['subject_group'])."' $selected>".htmlspecialchars($grp['subject_group'])."</option>";
            }
            ?>
        </select>
        <input type="text" name="search" placeholder="Search" value="<?=htmlspecialchars($search)?>">
        <button type="submit">Filter</button>
        <a href="?page=attendance_info"><button type="button">Reset</button></a>
    </form>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Subject</th>
            <th>Subject Group</th>
            <th>Status</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
        <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['student_name'])?></td>
                <td><?=htmlspecialchars($row['subject_name'])?></td>
                <td><?=htmlspecialchars($row['subject_group'])?></td>
                <td><?=htmlspecialchars($row['status'])?></td>
                <td><?=htmlspecialchars($row['date'])?></td>
                <td><?=htmlspecialchars($row['time'])?></td>
            </tr>
        <?php endwhile; ?>
    </table>

<?php
elseif($page=='classfees_info'):
    $dateFilter = $_GET['filter_date'] ?? '';
    $teacherFilter = $_GET['filter_teacher'] ?? '';
    $groupFilter = $_GET['filter_group'] ?? '';
    $search = $_GET['search'] ?? '';

    $query = "SELECT c.student_id, s.name AS student_name, t.name AS teacher_name, c.subject_group, c.class_fee, c.date, c.time
              FROM class_fees c
              JOIN student_register s ON c.student_id = s.student_id
              JOIN teachers_subjects_group tg ON c.subject_group = tg.subject_group
              JOIN teacher_register t ON tg.teacher_id = t.teacher_id
              WHERE 1=1";

    if($dateFilter) $query .= " AND c.date = '".$conn->real_escape_string($dateFilter)."'";
    if($teacherFilter) $query .= " AND t.name = '".$conn->real_escape_string($teacherFilter)."'";
    if($groupFilter) $query .= " AND c.subject_group = '".$conn->real_escape_string($groupFilter)."'";
    if($search) $query .= " AND (s.name LIKE '%".$conn->real_escape_string($search)."%' OR t.name LIKE '%".$conn->real_escape_string($search)."%')";

    $query .= " ORDER BY c.date DESC, c.time DESC";
    $res = $conn->query($query);
    if(!$res){ die("Query failed: ".$conn->error); }
    ?>
    <h1>Class Fees Info</h1>
    <form method="get" style="margin-bottom:20px;">
        <input type="hidden" name="page" value="classfees_info">
        <input type="date" name="filter_date" value="<?=htmlspecialchars($dateFilter)?>">
        <select name="filter_teacher">
            <option value="">All Teachers</option>
            <?php
            $teachers = $conn->query("SELECT DISTINCT name FROM teacher_register ORDER BY name ASC");
            while($tch = $teachers->fetch_assoc()){
                $selected = ($teacherFilter == $tch['name']) ? "selected" : "";
                echo "<option value='".htmlspecialchars($tch['name'])."' $selected>".htmlspecialchars($tch['name'])."</option>";
            }
            ?>
        </select>
        <select name="filter_group">
            <option value="">All Subject Groups</option>
            <?php
            $groups = $conn->query("SELECT DISTINCT subject_group FROM teachers_subjects_group ORDER BY subject_group ASC");
            while($grp = $groups->fetch_assoc()){
                $selected = ($groupFilter == $grp['subject_group']) ? "selected" : "";
                echo "<option value='".htmlspecialchars($grp['subject_group'])."' $selected>".htmlspecialchars($grp['subject_group'])."</option>";
            }
            ?>
        </select>
        <input type="text" name="search" placeholder="Search" value="<?=htmlspecialchars($search)?>">
        <button type="submit">Filter</button>
        <a href="?page=classfees_info"><button type="button">Reset</button></a>
    </form>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Teacher Name</th>
            <th>Subject Group</th>
            <th>Class Fee</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
        <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?=htmlspecialchars($row['student_name'])?></td>
                <td><?=htmlspecialchars($row['teacher_name'])?></td>
                <td><?=htmlspecialchars($row['subject_group'])?></td>
                <td><?=htmlspecialchars($row['class_fee'])?></td>
                <td><?=htmlspecialchars($row['date'])?></td>
                <td><?=htmlspecialchars($row['time'])?></td>
            </tr>
        <?php endwhile; ?>
    </table>

<?php
else:
    echo "<h1>Page Not Found</h1>";
endif;
?>
    </div>
</div>
</body>
</html>
