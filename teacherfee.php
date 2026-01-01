<?php
session_start();
// DB connection
$conn = new mysqli("localhost", "root", "", "anonymous_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch teachers for dropdown
$teachers = $conn->query("SELECT teacher_id, name FROM teacher_register");

// Initialize
$total_paid_students = 0;
$total_paid_students_fee = 0;
$institute_fee = 0;
$total_fee = 0;
$error = "";
$institute_rate = 0;
$bonus_fee = 0;
$teacher_id = '';
$month = '';

// Show calculation
if (isset($_POST['show'])) {
    $teacher_id = $_POST['teacher_id'];
    $month = $_POST['month'];
    $institute_rate = floatval($_POST['institute_rate'] ?? 0);
    $bonus_fee = floatval($_POST['bonus_fee'] ?? 0);

    // Get teacher's subject groups
    $subject_groups = $conn->query("SELECT subject_group FROM teachers_subjects_group WHERE teacher_id='$teacher_id'");
    $groups = [];
    while($row = $subject_groups->fetch_assoc()) $groups[] = $row['subject_group'];

    if(count($groups) > 0){
        $groups_list = "'".implode("','",$groups)."'";
        // Calculate total paid students & fees
        $sql = "SELECT COUNT(*) AS total_students, SUM(class_fee) AS total_fee 
                FROM class_fees 
                WHERE subject_group IN ($groups_list) AND month='$month'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $data = $result->fetch_assoc();
            $total_paid_students = $data['total_students'] ?? 0;
            $total_paid_students_fee = $data['total_fee'] ?? 0;

            $institute_fee = ($total_paid_students_fee * $institute_rate / 100);
            $total_fee = ($total_paid_students_fee - $institute_fee) + $bonus_fee;
        }
    } else {
        $error = "No subject groups found for this teacher.";
    }
}

// Save logic
if(isset($_POST['save'])){
    $teacher_id = $_POST['teacher_id'];
    $month = $_POST['month'];
    $institute_rate = floatval($_POST['institute_rate'] ?? 0);
    $bonus_fee = floatval($_POST['bonus_fee'] ?? 0);

    // Get teacher's subject groups
    $subject_groups = $conn->query("SELECT subject_group FROM teachers_subjects_group WHERE teacher_id='$teacher_id'");
    $groups = [];
    while($row = $subject_groups->fetch_assoc()) $groups[] = $row['subject_group'];

    if(count($groups) > 0){
        $groups_list = "'".implode("','",$groups)."'";
        // Calculate total paid students & fees again before save
        $sql = "SELECT COUNT(*) AS total_students, SUM(class_fee) AS total_fee 
                FROM class_fees 
                WHERE subject_group IN ($groups_list) AND month='$month'";
        $result = $conn->query($sql);
        $data = $result->fetch_assoc();
        $total_paid_students = $data['total_students'] ?? 0;
        $total_paid_students_fee = $data['total_fee'] ?? 0;

        $institute_fee = ($total_paid_students_fee * $institute_rate / 100);
        $total_fee = ($total_paid_students_fee - $institute_fee) + $bonus_fee;

        // Insert into teacher_salary
        $stmt = $conn->prepare("INSERT INTO teacher_salary 
            (teacher_id, total_paid_students, total_paid_students_fee, institute_rate, institute_fee, bonus_fee, total_fee) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idddddd",
            $teacher_id,
            $total_paid_students,
            $total_paid_students_fee,
            $institute_rate,
            $institute_fee,
            $bonus_fee,
            $total_fee
        );
        if($stmt->execute()){
            $error = "Saved successfully!";

            // Remove paid students from not_paid_students table
            $remove_sql = "DELETE n FROM not_paid_students n
                           INNER JOIN class_fees c ON n.student_id=c.student_id
                           WHERE c.subject_group IN ($groups_list) AND c.month='$month'";
            $conn->query($remove_sql);

        } else {
            $error = "Error saving data: ".$stmt->error;
        }
    } else {
        $error = "No subject groups found for this teacher.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Fee</title>
<style>
body { font-family: Arial; background: #f0f2f5; display:flex; justify-content:center; padding:50px; }
.card { background:#fff; padding:30px 40px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:500px; }
h2{text-align:center;margin-bottom:20px;}
form{display:flex; flex-direction:column; gap:15px;}
select,input{padding:10px;border-radius:8px;border:1px solid #ccc;font-size:16px;}
button{padding:12px;border:none;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;cursor:pointer;transition:0.3s;}
button:hover{transform:scale(1.05);}
.details p{margin:5px 0;font-weight:bold;}
.error{color:red;font-weight:bold;text-align:center;}
.success{color:green;font-weight:bold;text-align:center;}
.buttons{display:flex; gap:10px;}
</style>
</head>
<body>

<div class="card">
<h2>Teacher Fee</h2>
<?php if ($error) echo "<p class='".(strpos($error,'successfully')!==false?'success':'error')."'>$error</p>"; ?>

<form method="post">
<label>Teacher:</label>
<select name="teacher_id" required>
<option value="">Select Teacher</option>
<?php while($t = $teachers->fetch_assoc()): ?>
<option value="<?= $t['teacher_id'] ?>" <?= (isset($teacher_id)&&$teacher_id==$t['teacher_id'])?'selected':''; ?>><?= $t['name'] ?></option>
<?php endwhile; ?>
</select>

<label>Month:</label>
<select name="month" required>
<option value="">Select Month</option>
<?php 
$months=["January","February","March","April","May","June","July","August","September","October","November","December"];
foreach($months as $m): ?>
<option value="<?= $m ?>" <?= (isset($month)&&$month==$m)?'selected':''; ?>><?= $m ?></option>
<?php endforeach; ?>
</select>

<label>Institute Rate (%):</label>
<input type="number" step="0.01" name="institute_rate" value="<?= $institute_rate ?? 0 ?>">

<label>Bonus Fee:</label>
<input type="number" step="0.01" name="bonus_fee" value="<?= $bonus_fee ?? 0 ?>">

<div class="buttons">
<button type="submit" name="show">Show</button>
<button type="submit" name="save">Save</button>
</div>
</form>

<?php if(isset($_POST['show']) || isset($_POST['save'])): ?>
<div class="details">
<p>Total Paid Students: <?= $total_paid_students ?></p>
<p>Total Paid Students Fee: <?= number_format($total_paid_students_fee,2) ?></p>
<p>Institute Fee: <?= number_format($institute_fee,2) ?></p>
<p>Bonus Fee: <?= number_format($bonus_fee,2) ?></p>
<p><strong>Total Fee: <?= number_format($total_fee,2) ?></strong></p>
</div>
<?php endif; ?>

</div>
</body>
</html>
