<?php
// ----------------------------------------------------
// DATABASE CONNECTION
// ----------------------------------------------------
$conn = new mysqli("localhost", "root", "", "anonymous_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ====================================================
   1. TEACHER REGISTER — INSERT
   ==================================================== */
if (isset($_POST['teacher_register_btn'])) {
    $name = $_POST['t_name'];
    $nic = $_POST['t_nic'];
    $teach_subject = $_POST['t_subject'];
    $contact = $_POST['t_contact'];
    $address = $_POST['t_address'];
    $school = $_POST['t_school'];
    $stream = $_POST['t_stream'];

    $sql = "INSERT INTO teacher_register 
            (name, nic_number, teach_subject, contact_number, address, school, subject_stream)
            VALUES ('$name','$nic','$teach_subject','$contact','$address','$school','$stream')";

    if ($conn->query($sql)) {
        $success = "Teacher Registered Successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

/* ====================================================
   2. STUDENT REGISTER — INSERT
   ==================================================== */
if(isset($_POST['student_register_btn'])) {
    $name = $_POST['stu_name'];
    $nic = $_POST['stu_nic'];
    $contact = $_POST['stu_contact'];
    $address = $_POST['stu_address'];
    $school = $_POST['stu_school'];
    $pname = $_POST['stu_parent_name'];
    $pcontact = $_POST['stu_parent_contact'];
    $exam_year = $_POST['stu_exam_year'];

    $sql = "INSERT INTO student_register 
            (name, nic_number, contact_number, address, school, parent_name, parent_contactnumber, exam_year)
            VALUES ('$name','$nic','$contact','$address','$school','$pname','$pcontact','$exam_year')";

    if($conn->query($sql) === TRUE){
        $success = "Student Registered Successfully";
    } else {
        $error = "DB Error: ".$conn->error;
    }
}

/* ====================================================
   3. EMPLOYER REGISTER — INSERT
   ==================================================== */
if (isset($_POST['employer_register_btn'])) {
    $name = $_POST['emp_name'];
    $contact = $_POST['emp_contact'];
    $address = $_POST['emp_address'];
    $position = $_POST['emp_position'];
    $age = $_POST['emp_age'];

    $sql = "INSERT INTO employer_register 
            (name, contact_number, address, position, age)
            VALUES ('$name','$contact','$address','$position','$age')";

    if ($conn->query($sql)) {
        $success = "Employer Registered Successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

/* ====================================================
   4. TEACHER PRE-REGISTERED — SEARCH
   ==================================================== */
$tp_name = $tp_pic = $tp_contact = $tp_nic = $tp_subject = "";
if (isset($_POST['search_teacher_btn'])) {
    $tid = $_POST['search_teacher_id'];
    $q = $conn->query("SELECT * FROM teacher_register WHERE teacher_id='$tid' OR contact_number='$tid'");
    if ($q->num_rows > 0) {
        $row = $q->fetch_assoc();
        $tp_name = $row['name'];
        $tp_pic = $row['profile_picture'];
        $tp_contact = $row['contact_number'];
        $tp_nic = $row['nic_number'];
        $tp_subject = $row['teach_subject'];
    } else {
        $error = "No Teacher Found";
    }
}

/* ====================================================
   4b. TEACHER SUBJECT GROUP — INSERT
   ==================================================== */
if (isset($_POST['teacher_subject_save_btn'])) {
    $tid = $_POST['teacher_pre_id'];
    $subject_group = $_POST['teacher_subject_group'];
    $sql = "INSERT INTO teachers_subjects_group (teacher_id, subject_group) VALUES ('$tid', '$subject_group')";
    if ($conn->query($sql)) {
        $success = "Teacher Subject Group Saved";
    }
}

/* ====================================================
   5. STUDENT PRE-REGISTERED — SEARCH
   ==================================================== */
$sp_name = $sp_pic = $sp_contact = $sp_nic = "";
if (isset($_POST['search_student_btn'])) {
    $sid = $_POST['search_student_id'];
    $q = $conn->query("SELECT * FROM student_register WHERE student_id='$sid' OR contact_number='$sid'");
    if ($q->num_rows > 0) {
        $row = $q->fetch_assoc();
        $sp_name = $row['name'];
        $sp_pic = $row['profile_picture'];
        $sp_contact = $row['contact_number'];
        $sp_nic = $row['nic_number'];
    } else {
        $error = "No Student Found";
    }
}

/* ====================================================
   5b. STUDENT SUBJECT GROUP — INSERT
   ==================================================== */
if (isset($_POST['student_subject_save_btn'])) {
    $sid = $_POST['student_pre_id'];
    $subject = $_POST['student_subject'];
    $subject_group = $_POST['student_subject_group'];
    $sql = "INSERT INTO students_subject_group (student_id, subject, subject_group)
            VALUES ('$sid','$subject','$subject_group')";
    if ($conn->query($sql)) {
        $success = "Student Subject Assigned";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modern Registration Layout</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root{
    --primary: #4dabf7;
    --accent: #1c7ed6;
    --glass: rgba(255,255,255,0.05);
    --text-light: #f1f1f1;
}
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0a0a0a, #1a1a2e);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.card-glass{
    backdrop-filter: blur(15px);
    background: var(--glass);
    border-radius: 20px;
    padding: 40px;
    max-width: 700px;
    width: 90%;
    box-shadow: 0 0 40px rgba(0,0,0,0.7);
}
h2{ color: var(--primary); text-align: center; margin-bottom: 20px; }
.nav-tabs{ display: flex; justify-content: center; margin-bottom: 20px; }
.nav-tabs .nav-link{
    background: rgba(255,255,255,0.05);
    color: var(--text-light);
    border-radius: 50px 50px 0 0;
    margin: 0 5px;
    transition: 0.3s;
}
.nav-tabs .nav-link.active{ background: var(--primary); color: #fff; }
.input-icon{ position: relative; margin-bottom: 15px; }
.input-icon i{ position: absolute; top: 50%; left: 12px; transform: translateY(-50%); color: #aaa; }
input, select{ background: rgba(255,255,255,0.08); color: var(--text-light); border: 1px solid #444; border-radius: 10px; padding: 12px 12px 12px 40px; width: 100%; transition: 0.3s; }
input:focus, select:focus{ outline: none; box-shadow: 0 0 10px var(--primary); }
select{ background: #2a2a2a; color: var(--text-light); border: 1px solid var(--primary); padding-left: 12px; }
button{ background: var(--primary); border: none; color: #fff; padding: 12px; border-radius: 10px; width: 100%; font-weight: 600; transition: 0.3s; }
button:hover{ background: var(--accent); transform: scale(1.02); }
.alert{ text-align: center; margin-bottom: 20px; }
input[readonly]{ background: #2a3e5c; color: #cbd6e3; }
@media(max-width:768px){ .card-glass{ padding: 20px; } input, select{ padding-left: 35px; } }
</style>
</head>
<body>

<div class="card-glass">
<h2>Register</h2>

<?php
if(isset($success)) echo '<div class="alert alert-success">'.$success.'</div>';
if(isset($error)) echo '<div class="alert alert-danger">'.$error.'</div>';
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#teacher"><i class="fa fa-chalkboard-teacher me-1"></i>Teacher</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#student"><i class="fa fa-user-graduate me-1"></i>Student</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employer"><i class="fa fa-briefcase me-1"></i>Employer</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#teacher_pre"><i class="fa fa-user-tie me-1"></i>Teacher Pre</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#student_pre"><i class="fa fa-user-graduate me-1"></i>Student Pre</button></li>
</ul>

<div class="tab-content">

<!-- Teacher Register -->
<div class="tab-pane fade show active" id="teacher">
<form method="POST">
<div class="input-icon"><i class="fa fa-user"></i><input type="text" name="t_name" placeholder="Name" required></div>
<div class="input-icon"><i class="fa fa-id-card"></i><input type="text" name="t_nic" placeholder="NIC" required></div>
<div class="input-icon"><i class="fa fa-book"></i><input type="text" name="t_subject" placeholder="Teaching Subject" required></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" name="t_contact" placeholder="Contact" required></div>
<div class="input-icon"><i class="fa fa-home"></i><input type="text" name="t_address" placeholder="Address" required></div>
<div class="input-icon"><i class="fa fa-school"></i><input type="text" name="t_school" placeholder="School" required></div>
<div class="input-icon"><i class="fa fa-stream"></i><input type="text" name="t_stream" placeholder="Stream" required></div>
<button name="teacher_register_btn">Register</button>
</form>
</div>

<!-- Student Register -->
<div class="tab-pane fade" id="student">
<form method="POST">
<div class="input-icon"><i class="fa fa-user"></i><input type="text" name="stu_name" placeholder="Name" required></div>
<div class="input-icon"><i class="fa fa-id-card"></i><input type="text" name="stu_nic" placeholder="NIC Number" required></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" name="stu_contact" placeholder="Contact Number" required></div>
<div class="input-icon"><i class="fa fa-home"></i><input type="text" name="stu_address" placeholder="Address" required></div>
<div class="input-icon"><i class="fa fa-school"></i><input type="text" name="stu_school" placeholder="School" required></div>
<div class="input-icon"><i class="fa fa-user"></i><input type="text" name="stu_parent_name" placeholder="Parent Name" required></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" name="stu_parent_contact" placeholder="Parent Contact Number" required></div>
<div class="input-icon"><i class="fa fa-calendar"></i><input type="text" name="stu_exam_year" placeholder="Exam Year" required></div>
<button type="submit" name="student_register_btn">Register</button>
</form>
</div>

<!-- Employer Register -->
<div class="tab-pane fade" id="employer">
<form method="POST">
<div class="input-icon"><i class="fa fa-user"></i><input type="text" name="emp_name" placeholder="Name" required></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" name="emp_contact" placeholder="Contact" required></div>
<div class="input-icon"><i class="fa fa-home"></i><input type="text" name="emp_address" placeholder="Address" required></div>
<div class="input-icon"><i class="fa fa-briefcase"></i><input type="text" name="emp_position" placeholder="Position" required></div>
<div class="input-icon"><i class="fa fa-calendar"></i><input type="text" name="emp_age" placeholder="Age" required></div>
<button name="employer_register_btn">Register</button>
</form>
</div>

<!-- Teacher Pre Registered -->
<div class="tab-pane fade" id="teacher_pre">
<form method="POST">
<div class="input-icon"><i class="fa fa-search"></i><input type="text" name="search_teacher_id" placeholder="Teacher ID / Contact" required></div>
<button name="search_teacher_btn">Search</button>
</form>

<form method="POST">
<div class="input-icon"><i class="fa fa-user"></i><input type="text" value="<?= $tp_name ?>" readonly></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" value="<?= $tp_contact ?>" readonly></div>
<div class="input-icon"><i class="fa fa-id-card"></i><input type="text" value="<?= $tp_nic ?>" readonly></div>
<div class="input-icon"><i class="fa fa-book"></i><input type="text" value="<?= $tp_subject ?>" readonly></div>
<input type="hidden" name="teacher_pre_id" value="<?= $_POST['search_teacher_id'] ?? '' ?>">
<div class="input-icon"><i class="fa fa-layer-group"></i><input type="text" name="teacher_subject_group" placeholder="Subject Group"></div>
<button name="teacher_subject_save_btn">Save Group</button>
</form>
</div>

<!-- Student Pre Registered -->
<div class="tab-pane fade" id="student_pre">
<form method="POST">
<div class="input-icon"><i class="fa fa-search"></i><input type="text" name="search_student_id" placeholder="Student ID / Contact" required></div>
<button name="search_student_btn">Search</button>
</form>

<form method="POST">
<div class="input-icon"><i class="fa fa-user"></i><input type="text" value="<?= $sp_name ?>" readonly></div>
<div class="input-icon"><i class="fa fa-phone"></i><input type="text" value="<?= $sp_contact ?>" readonly></div>
<div class="input-icon"><i class="fa fa-id-card"></i><input type="text" value="<?= $sp_nic ?>" readonly></div>
<input type="hidden" name="student_pre_id" value="<?= $_POST['search_student_id'] ?? '' ?>">
<div class="input-icon"><i class="fa fa-book"></i><input type="text" name="student_subject" placeholder="Subject"></div>
<div class="input-icon">
<select name="student_subject_group">
<?php
$q = $conn->query("SELECT DISTINCT subject_group FROM teachers_subjects_group");
while ($r = $q->fetch_assoc()) {
    echo "<option value='{$r['subject_group']}'>{$r['subject_group']}</option>";
}
?>
</select>
</div>
<button name="student_subject_save_btn">Assign Subject</button>
</form>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
