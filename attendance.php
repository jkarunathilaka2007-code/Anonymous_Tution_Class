<?php
session_start();
include 'db_connect.php';

$student = null;
$subjects = [];
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Handle attendance form submission
if(isset($_POST['mark_attendance'])){
    $student_id = $_POST['student_id'];
    $subject_group = $_POST['subject_group'];
    $status = $_POST['status'];
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $stmt_check = $conn->prepare("SELECT * FROM attendance WHERE student_id=? AND subject_group=? AND date=?");
    $stmt_check->bind_param("sss", $student_id, $subject_group, $date);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        $message = "Attendance already marked for this student!";
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (student_id,subject_group,status,date,time) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $student_id,$subject_group,$status,$date,$time);
        if($stmt->execute()) $message = "Attendance marked successfully!";
        else $message = "Failed to mark attendance.";
        $stmt->close();
    }
    $stmt_check->close();
}

// Search student
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM student_register WHERE student_id=? OR contact_number=?");
    $stmt->bind_param("ss", $search,$search);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $student = $result->fetch_assoc();
        $stmt2 = $conn->prepare("SELECT subject_group FROM students_subject_group WHERE student_id=?");
        $stmt2->bind_param("s",$student['student_id']);
        $stmt2->execute();
        $subjects_result = $stmt2->get_result();
        while($row = $subjects_result->fetch_assoc()) $subjects[] = $row['subject_group'];
        $stmt2->close();
    } else {
        $message = "Student not found!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
<style>
:root{
    --primary:#4dabf7;
    --secondary:#1f1f1f;
    --bg:#0e1326;
    --card:#1f1f2e;
    --text:#f1f1f1;
    --hover:#00d4ff;
    --accent:#ff6b81;
}

*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body,html{
    min-height:100vh;
    background: linear-gradient(135deg,#0e1326,#1f1f3a,#0e1326);
    background-size:400% 400%;
    animation: gradientBG 15s ease infinite;
    color: var(--text);
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:30px 0;
}
@keyframes gradientBG{0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}

/* Container */
.container{
    width:90%;
    max-width:900px;
    display:flex;
    flex-direction:column;
    gap:25px;
}

/* Cards */
.card{
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    border-radius:25px;
    padding:30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    transition:0.4s;
}
.card:hover{
    transform: translateY(-8px);
    box-shadow:0 20px 60px rgba(0,0,0,0.5);
}

/* Forms */
input, select, button{
    width:100%;
    padding:14px;
    margin:10px 0 18px;
    border-radius:12px;
    border:none;
    background:rgba(255,255,255,0.1);
    color:#000; /* dropdown & input text color black */
    font-size:16px;
}
input:focus, select:focus{
    outline:none;
    box-shadow:0 0 12px var(--primary);
}
button{
    background: linear-gradient(45deg,var(--primary),var(--hover));
    color:#000;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
    font-size:16px;
}
button:hover{
    transform:scale(1.05);
}

/* Student Info */
.student-info{
    display:flex;
    align-items:center;
    gap:25px;
}
.student-info img{
    width:120px;
    height:120px;
    border-radius:50%;
    border:4px solid var(--primary);
    object-fit:cover;
}
.student-info div h2{
    margin-bottom:10px;
    font-size:24px;
    color:var(--primary);
}
.student-info div p{
    margin:4px 0;
    font-size:16px;
}

/* Messages */
.message{
    padding:15px;
    background: rgba(0,212,255,0.25);
    border-radius:15px;
    text-align:center;
    font-weight:600;
    color:#000;
    font-size:16px;
    animation:fadeIn 0.8s forwards;
}

/* Headers */
h2{
    margin-bottom:15px;
    font-size:22px;
    color: var(--hover);
}
</style>
</head>
<body>
<div class="container">

<?php if($message): ?>
<div class="card message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
    <h2>Search Student</h2>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Enter Student ID or Contact Number" required>
        <button type="submit">Search</button>
    </form>
</div>

<?php if($student): ?>
<div class="card student-info">
    <?php
    $profilePath = '';
    if(!empty($student['profile_picture'])){
        $path = 'profiles/' . $student['profile_picture'];
        if(file_exists($path)) $profilePath = $path;
    }
    ?>
    <?php if($profilePath): ?>
        <img src="<?= $profilePath ?>" alt="Profile">
    <?php endif; ?>
    <div>
        <h2><?= htmlspecialchars($student['name']) ?></h2>
        <p><strong>Contact:</strong> <?= htmlspecialchars($student['contact_number']) ?></p>
        <p><strong>Exam Year:</strong> <?= htmlspecialchars($student['exam_year']) ?></p>
    </div>
</div>

<div class="card">
    <h2>Mark Attendance</h2>
    <form method="POST" action="">
        <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>">
        <select name="subject_group" required>
            <option value="">--Select Subject Group--</option>
            <?php foreach($subjects as $sub): ?>
                <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" required>
            <option value="true">Present</option>
            <option value="false">Absent</option>
        </select>
        <button type="submit" name="mark_attendance">Mark Attendance</button>
    </form>
</div>
<?php endif; ?>

</div>
</body>
</html>
