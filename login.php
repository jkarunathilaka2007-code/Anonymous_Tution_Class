<?php
session_start();
include 'db_connect.php';

$msg = '';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Admin login
    if($username === 'admin' && $password === '123456'){
        $_SESSION['admin'] = 'admin';
        header('Location: admindashboard.php');
        exit();
    }

    // Student login
    $stmt = $conn->prepare("SELECT * FROM student_register WHERE contact_number=?");
    if($stmt){
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            if(password_verify($password,$row['password'])){
                $_SESSION['student'] = $row;
                header('Location: studentdashboard.php');
                exit();
            }
        }
    }

    // Teacher login
    $stmt = $conn->prepare("SELECT * FROM teacher_register WHERE contact_number=?");
    if($stmt){
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            if(password_verify($password,$row['password'])){
                $_SESSION['teacher'] = $row;
                header('Location: teacherdashboard.php');
                exit();
            }
        }
    }

    // Employer login
    $stmt = $conn->prepare("SELECT * FROM employer_register WHERE contact_number=?");
    if($stmt){
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            if(password_verify($password,$row['password'])){
                $_SESSION['employer'] = $row;
                header('Location: employerdashboard.php');
                exit();
            }
        }
    }

    $msg = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    /* Body & Background */
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to right, #2c5364, #203a43, #0f2027);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    /* Floating Shapes */
    .shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.3;
        animation: float 10s infinite ease-in-out alternate;
        z-index: 0;
    }
    .shape1 { width: 200px; height: 200px; background: #ff5f6d; top: -50px; left: -50px; animation-delay: 0s; }
    .shape2 { width: 300px; height: 300px; background: #ffc371; bottom: -100px; right: -100px; animation-delay: 2s; }
    .shape3 { width: 150px; height: 150px; background: #24c6dc; top: 20%; right: 10%; animation-delay: 4s; }
    @keyframes float {
        0% { transform: translateY(0) rotate(0deg);}
        100% { transform: translateY(25px) rotate(45deg);}
    }

    /* Login Card */
    .login-container {
        position: relative;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 50px 40px;
        width: 380px;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        z-index: 1;
        animation: cardFadeIn 1s ease forwards;
    }
    @keyframes cardFadeIn {
        0% { opacity: 0; transform: translateY(-30px);}
        100% { opacity: 1; transform: translateY(0);}
    }

    .login-container h2 {
        color: #fff;
        margin-bottom: 30px;
        font-size: 28px;
    }

    /* Input Fields */
    .login-container input {
        width: 100%;
        padding: 15px;
        margin: 12px 0;
        border: none;
        border-radius: 12px;
        outline: none;
        font-size: 16px;
        transition: 0.3s;
        background: rgba(255,255,255,0.2);
        color: #fff;
    }
    .login-container input::placeholder { color: #e0e0e0; }
    .login-container input:focus {
        background: rgba(255,255,255,0.3);
        box-shadow: 0 0 10px rgba(255,255,255,0.5);
    }

    /* Login Button */
    .login-container button {
        width: 100%;
        padding: 15px;
        margin-top: 20px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        background: linear-gradient(135deg, #ff5f6d, #ffc371);
        transition: all 0.3s ease;
    }
    .login-container button:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(255,255,255,0.3);
    }

    /* Error Message */
    .msg {
        color: #ff6b6b;
        margin-bottom: 10px;
        font-weight: 500;
    }

    /* Links */
    .login-links {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        font-size: 14px;
    }
    .login-links a {
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .login-links a:hover {
        text-decoration: underline;
        color: #ffc371;
    }
    .login-links .back-btn {
        background: rgba(255,255,255,0.2);
        padding: 5px 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .login-links .back-btn:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
    }

    /* Responsive */
    @media(max-width: 420px){
        .login-container { width: 90%; padding: 40px 20px; }
    }
</style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>

    <!-- Login Card -->
    <div class="login-container">
        <h2>Login</h2>
        <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Contact Number / Admin" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Forget Password & Back -->
        <div class="login-links">
            <a href="password.php">Forget Password?</a>
            <a href="index.php" class="back-btn">Back</a>
        </div>
    </div>
</body>
</html>
