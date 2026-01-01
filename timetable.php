<?php
include 'db_connect.php';

$teacher_id = intval($_GET['teacher_id'] ?? 0);
if($teacher_id == 0){
    echo "Teacher not found!";
    exit;
}

// Fetch teacher info
$teacher_res = $conn->query("SELECT * FROM teacher_register WHERE teacher_id=$teacher_id");
$teacher = $teacher_res->fetch_assoc();

// Fetch timetable(s)
$timetable_res = $conn->query("SELECT * FROM timetable WHERE teacher_id=$teacher_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $teacher['name']; ?> - Timetable</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

/* Body & background */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    background: linear-gradient(135deg, #2c5364, #203a43, #0f2027);
    overflow-x: hidden;
    padding: 50px 20px;
    position: relative;
}

/* Floating shapes */
.shape {
    position: absolute;
    border-radius: 50%;
    opacity: 0.4;
    animation: float 10s infinite alternate;
    pointer-events: none;
}
.shape1 { width: 150px; height: 150px; background: #ff5f6d; top: 10%; left: 5%; }
.shape2 { width: 200px; height: 200px; background: #ffc371; top: 60%; left: 70%; }
.shape3 { width: 120px; height: 120px; background: #24c6dc; top: 40%; left: 30%; }

@keyframes float {
    0% { transform: translateY(0px) translateX(0px);}
    50% { transform: translateY(-20px) translateX(20px);}
    100% { transform: translateY(0px) translateX(0px);}
}

/* Container */
.container {
    width: 100%;
    max-width: 1000px;
    backdrop-filter: blur(15px);
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    padding: 40px 30px;
    animation: fadeInUp 1s ease forwards;
}

/* Fade-in animation */
@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(-20px);}
    100% { opacity: 1; transform: translateY(0);}
}

/* Headings */
h1, h2, h3 {
    color: #fff;
    margin: 0 0 15px;
    font-weight: 600;
    text-align: center;
}

h1 { font-size: 2.8em; }
h2 { font-size: 1.5em; margin-bottom: 40px; }
h3 { font-size: 1.4em; background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 15px; margin-top: 30px; }

/* Table */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 15px;
    overflow: hidden;
    margin-top: 15px;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.05);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

table th, table td {
    padding: 15px;
    text-align: center;
}

table th {
    background: rgba(255,255,255,0.2);
    color: #fff;
    font-weight: 600;
}

table td {
    background: rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

table tr:last-child td {
    border-bottom: none;
}

/* Paragraphs */
p {
    text-align: center;
    color: #ffc371;
    font-size: 1.1em;
}

/* Buttons */
button {
    background: linear-gradient(90deg, #ff5f6d, #ffc371);
    color: #fff;
    font-weight: 600;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 20px rgba(255, 95, 109, 0.5);
}

/* Responsive */
@media(max-width:768px){
    h1 { font-size: 2em; }
    h2 { font-size: 1.2em; margin-bottom: 20px; }
    table th, table td { padding: 10px; font-size: 0.9em; }
    .container { padding: 30px 20px; }
}
</style>
</head>
<body>

<!-- Floating shapes -->
<div class="shape shape1"></div>
<div class="shape shape2"></div>
<div class="shape shape3"></div>

<div class="container">
    <h1><?php echo $teacher['name']; ?>'s Timetable</h1>
    <h2>Subjects: <?php echo $teacher['teach_subject']; ?></h2>

    <?php
    if($timetable_res->num_rows == 0){
        echo "<p>No timetable available for this teacher.</p>";
    } else {
        while($t = $timetable_res->fetch_assoc()){
            $tt = json_decode($t['timetable'], true);
            echo "<h3>".$t['timetable_name']."</h3>";
            echo "<table><tr>";
            foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day) echo "<th>$day</th>";
            echo "</tr>";
            foreach($tt as $row){
                echo "<tr>";
                foreach($row as $cell){
                    echo "<td>".$cell['time']."<br><strong>".$cell['subject']."</strong></td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    ?>
</div>

</body>
</html>
