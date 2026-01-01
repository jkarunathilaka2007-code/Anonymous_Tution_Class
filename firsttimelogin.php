<?php
session_start();
require "db_connection.php";

if (!isset($_SESSION['pending_user'])) {
    die("Unauthorized access.");
}

$pending = $_SESSION['pending_user'];
$user_id = $pending['id'];
$table = $pending['type'];
$message = "";

$primary_keys = [
    "student_register" => "student_id",
    "teacher_register" => "teacher_id",
    "employer_register" => "employer_id"
];

if (!isset($primary_keys[$table])) die("Invalid user type!");
$pk = $primary_keys[$table];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw1 = trim($_POST['password']);
    $pw2 = trim($_POST['confirm_password']);

    if ($pw1 !== $pw2) {
        $message = "Passwords do not match!";
    } elseif (strlen($pw1) < 4) {
        $message = "Password must be at least 4 characters!";
    } else {
        $hash = password_hash($pw1, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE $table SET password=? WHERE $pk=?");
        if (!$stmt) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("si", $hash, $user_id);

            if ($stmt->execute()) {
                unset($_SESSION['pending_user']);

                // Auto-login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_role'] = $table;
                $_SESSION['user_name'] = $pending['name'];

                switch ($table) {
                    case "student_register": header("Location: studentdashboard.php"); exit();
                    case "teacher_register": header("Location: teacherdashboard.php"); exit();
                    case "employer_register": header("Location: employerdashboard.php"); exit();
                }
            } else {
                $message = "Execute failed: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Password</title>
</head>
<body>
<h2>First-time Login: Create Your Password</h2>
<?php if($message): ?>
<p style="color:red;"><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST">
New Password:<br>
<input type="password" name="password" required><br><br>

Confirm Password:<br>
<input type="password" name="confirm_password" required><br><br>

<button type="submit">Save Password</button>
</form>
</body>
</html>
