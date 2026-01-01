<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "anonymous_db"; // your DB name

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all subject groups for filter dropdown
$subjectGroups = [];
$sg_result = $conn->query("SELECT DISTINCT subject_group FROM class_fees");
while($row = $sg_result->fetch_assoc()){
    $subjectGroups[] = $row['subject_group'];
}

// Handle search & filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_subject = isset($_GET['filter_subject']) ? $_GET['filter_subject'] : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$query = "SELECT cf.*, sr.name, sr.contact_number FROM class_fees cf 
          JOIN student_register sr ON cf.student_id = sr.student_id 
          WHERE 1=1 ";

$params = [];
$types = "";

// Search
if($search != ''){
    $query .= " AND (sr.name LIKE ? OR sr.student_id LIKE ? OR sr.contact_number LIKE ?) ";
    $likeSearch = "%$search%";
    $params[] = &$likeSearch;
    $params[] = &$likeSearch;
    $params[] = &$likeSearch;
    $types .= "sss";
}

// Filter subject
if($filter_subject != ''){
    $query .= " AND cf.subject_group = ? ";
    $params[] = &$filter_subject;
    $types .= "s";
}

// Filter date
if($filter_date != ''){
    $query .= " AND cf.date = ? ";
    $params[] = &$filter_date;
    $types .= "s";
}

$query .= " ORDER BY cf.date DESC ";

$stmt = $conn->prepare($query);
if($stmt === false){
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Fees Info</title>
    <style>
        table, th, td { border:1px solid black; border-collapse: collapse; padding:5px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h2>Class Fees Information</h2>

<form method="get">
    <label>Search (Name, ID, Contact):</label>
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">

    <label>Filter by Subject:</label>
    <select name="filter_subject">
        <option value="">All</option>
        <?php foreach($subjectGroups as $sg){
            $selected = ($filter_subject == $sg) ? "selected" : "";
            echo "<option value='$sg' $selected>$sg</option>";
        } ?>
    </select>

    <label>Filter by Date:</label>
    <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">

    <input type="submit" value="Filter">
</form>

<br>

<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Subject Group</th>
        <th>Class Fee</th>
        <th>Month</th>
        <th>Date</th>
        <th>Time</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['student_id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['contact_number']; ?></td>
        <td><?php echo $row['subject_group']; ?></td>
        <td><?php echo $row['class_fee']; ?></td>
        <td><?php echo $row['month']; ?></td>
        <td><?php echo $row['date']; ?></td>
        <td><?php echo $row['time']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
