<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";
$dbname = "anonymous_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$student = null;
$subjects = [];
$payment_history = [];
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

// Search student
if (isset($_POST['search_student'])) {
    $search = $_POST['search_value'];
    $stmt = $conn->prepare("SELECT * FROM student_register WHERE student_id=? OR contact_number=?");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if ($student) {
        $stmt2 = $conn->prepare("SELECT subject_group FROM students_subject_group WHERE student_id=?");
        $stmt2->bind_param("i", $student['student_id']);
        $stmt2->execute();
        $subjects = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt3 = $conn->prepare("SELECT subject_group, month, class_fee FROM class_fees WHERE student_id=? ORDER BY date ASC");
        $stmt3->bind_param("i", $student['student_id']);
        $stmt3->execute();
        $payment_history = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// Pay fee
if (isset($_POST['pay_fee'])) {
    $student_id = $_POST['student_id'];
    $subject_group = $_POST['subject_group'];
    $class_fee = $_POST['class_fee'];
    $month = $_POST['month'];
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $stmt = $conn->prepare("INSERT INTO class_fees (student_id,class_fee,subject_group,month,date,time) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("idssss", $student_id,$class_fee,$subject_group,$month,$date,$time);
    $stmt->execute();
    echo "<script>alert('Class fee saved successfully!'); window.location.href='classfees.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Fee Payment</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
    --primary:#4dabf7;
    --secondary:#1f1f1f;
    --bg:#0e1326;
    --card:#1f1f2e;
    --text:#f1f1f1;
    --hover:#00d4ff;
}

/* Reset */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body, html{height:100%;overflow-x:hidden;}

/* Gradient animated background */
body{
    background: linear-gradient(135deg,#0e1326,#1f1f3a,#0e1326);
    background-size:400% 400%;
    animation: gradientBG 15s ease infinite;
    color:var(--text);
    position:relative;
}
@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

/* Floating shapes */
.shape{position:absolute;border-radius:50%;opacity:0.15;animation: floatShape 20s linear infinite;}
.shape1{width:150px;height:150px;background:#4dabf7;top:10%;left:5%;}
.shape2{width:100px;height:100px;background:#00d4ff;top:50%;left:80%;}
.shape3{width:200px;height:200px;background:#ff6b81;top:80%;left:20%;}
@keyframes floatShape{
    0%{transform: translateY(0px) translateX(0px);}
    50%{transform: translateY(-30px) translateX(20px);}
    100%{transform: translateY(0px) translateX(0px);}
}

/* Container */
.container{width:90%;max-width:1000px;margin:50px auto;position:relative; z-index:1;}

/* Cards & forms */
.card{background: rgba(255,255,255,0.05);backdrop-filter:blur(10px);border-radius:20px;padding:25px;margin-bottom:20px;box-shadow:0 8px 32px rgba(0,212,255,0.2);transition:0.3s;opacity:0;animation:fadeIn 1s forwards;}
.card:hover{transform:translateY(-5px);box-shadow:0 0 30px rgba(0,212,255,0.3);}
@keyframes fadeIn{to{opacity:1;}}

/* Forms */
input, select{width:100%;padding:10px;margin:5px 0 15px;border-radius:10px;border:none;background:rgba(255,255,255,0.1);color:#fff;}
input:focus, select:focus{outline:none;box-shadow:0 0 10px var(--primary);}
input[type=submit]{background:linear-gradient(45deg,#4dabf7,#00d4ff);color:#000;font-weight:700;cursor:pointer;transition:0.3s;}
input[type=submit]:hover{transform:scale(1.05);}

/* Student info */
.student-info img{width:100px;height:100px;border-radius:50%;border:3px solid var(--primary);margin-top:10px;}

/* Chart filters */
label{margin-right:10px;font-weight:500;}
</style>
</head>
<body>

<!-- Floating shapes -->
<div class="shape shape1"></div>
<div class="shape shape2"></div>
<div class="shape shape3"></div>

<div class="container">
    <div class="card">
        <h2>Search Student</h2>
        <form method="post">
            <input type="text" name="search_value" placeholder="Student ID or Contact Number" required>
            <input type="submit" name="search_student" value="Search">
        </form>
    </div>

    <?php if($student): ?>
    <div class="card student-info">
        <h2>Student Info</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($student['contact_number']); ?></p>
        <p><strong>Exam Year:</strong> <?php echo htmlspecialchars($student['exam_year']); ?></p>
        <?php if(!empty($student['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile">
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Pay Class Fee</h2>
        <form method="post">
            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
            <select name="subject_group" required>
                <option value="">--Select Subject Group--</option>
                <?php foreach($subjects as $sub): ?>
                    <option value="<?php echo htmlspecialchars($sub['subject_group']); ?>"><?php echo htmlspecialchars($sub['subject_group']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="class_fee" step="0.01" placeholder="Class Fee" required>
            <select name="month" required>
                <option value="">--Select Month--</option>
                <?php foreach($months as $m){ echo "<option value='$m'>$m</option>"; } ?>
            </select>
            <input type="submit" name="pay_fee" value="Pay">
        </form>
    </div>

    <?php if(!empty($payment_history)): ?>
    <div class="card">
        <h2>Payment History</h2>
        <label>Filter by Month:</label>
        <select id="filterMonth">
            <option value="">All</option>
            <?php foreach($months as $m){ echo "<option value='$m'>$m</option>"; } ?>
        </select>
        <label>Filter by Subject:</label>
        <select id="filterSubject">
            <option value="">All</option>
            <?php $uniqueSubjects = array_unique(array_column($payment_history,'subject_group')); foreach($uniqueSubjects as $subj){ echo "<option value='$subj'>$subj</option>"; } ?>
        </select>
        <canvas id="paymentChart" width="600" height="300" style="max-width:100%;"></canvas>
        <script>
        var allData = <?php echo json_encode($payment_history); ?>;
        var subjectColors = {'English':'rgba(54,162,235,0.7)','Maths':'rgba(255,99,132,0.7)','Science':'rgba(255,206,86,0.7)','History':'rgba(75,192,192,0.7)'};

        function updateChart(){
            var monthFilter = document.getElementById('filterMonth').value;
            var subjectFilter = document.getElementById('filterSubject').value;
            var filtered = allData.filter(p => (monthFilter===''||p.month===monthFilter) && (subjectFilter===''||p.subject_group===subjectFilter));
            var labels = filtered.map(p=>p.month+' - '+p.subject_group);
            var data = filtered.map(p=>p.class_fee);
            var colors = filtered.map(p=>subjectColors[p.subject_group]||'rgba(150,150,150,0.7)');

            paymentChart.data.labels = labels;
            paymentChart.data.datasets[0].data = data;
            paymentChart.data.datasets[0].backgroundColor = colors;
            paymentChart.data.datasets[0].borderColor = colors.map(c=>c.replace('0.7','1'));
            paymentChart.update();
        }

        var ctx = document.getElementById('paymentChart').getContext('2d');
        var paymentChart = new Chart(ctx,{
            type:'bar',
            data:{labels:allData.map(p=>p.month+' - '+p.subject_group),
                  datasets:[{label:'Class Fee Paid',data:allData.map(p=>p.class_fee),
                             backgroundColor:allData.map(p=>subjectColors[p.subject_group]||'rgba(150,150,150,0.7)'),
                             borderColor:allData.map(p=>subjectColors[p.subject_group]?subjectColors[p.subject_group].replace('0.7','1'):'rgba(150,150,150,1)'),
                             borderWidth:1}]},
            options:{responsive:true,scales:{y:{beginAtZero:true}}}
        });

        document.getElementById('filterMonth').addEventListener('change', updateChart);
        document.getElementById('filterSubject').addEventListener('change', updateChart);
        </script>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>
</body>
</html>
