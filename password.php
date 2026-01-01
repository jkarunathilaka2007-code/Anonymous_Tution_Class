<?php
session_start();
include 'db_connect.php';

$msgAdd = '';
$msgForget = '';
$user = null;

function getUserDetails($conn, $contact) {
    $tables = ['student_register', 'teacher_register', 'employer_register'];
    foreach($tables as $table){
        $stmt = $conn->prepare("SELECT * FROM $table WHERE contact_number=?");
        $stmt->bind_param("s",$contact);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $row['table'] = $table;
            return $row;
        }
    }
    return null;
}

// ===================== Forget Password Tab =====================
if(isset($_POST['forget_check'])){
    $contact = $_POST['forget_contact'];
    $user = getUserDetails($conn, $contact);
    if(!$user){
        $msgForget = "No user found with this contact number!";
    }
}

// Set password = NULL
if(isset($_POST['forget_clear'])){
    $contact = $_POST['forget_contact'];
    $user = getUserDetails($conn, $contact);
    if($user){
        $stmt = $conn->prepare("UPDATE {$user['table']} SET password=NULL WHERE contact_number=?");
        $stmt->bind_param("s",$contact);
        if($stmt->execute()){
            $msgForget = "Password cleared. Now enter new password.";
        }else{
            $msgForget = "Failed to clear password.";
        }
    }else{
        $msgForget = "No user found with this contact number!";
    }
}

// Save new password in forget tab
if(isset($_POST['forget_save'])){
    $contact = $_POST['forget_contact'];
    $password = $_POST['forget_password'];
    $confirm = $_POST['forget_confirm'];
    $user = getUserDetails($conn, $contact);

    if(!$user){
        $msgForget = "No user found with this contact number!";
    }elseif($password !== $confirm){
        $msgForget = "Passwords do not match!";
    }else{
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE {$user['table']} SET password=? WHERE contact_number=?");
        $stmt->bind_param("ss",$hashed,$contact);
        if($stmt->execute()){
            $msgForget = "Password successfully updated!";
        }else{
            $msgForget = "Failed to update password.";
        }
    }
}

// ===================== Add Password Tab =====================
if(isset($_POST['add_save'])){
    $contact = $_POST['add_contact'];
    $password = $_POST['add_password'];
    $confirm = $_POST['add_confirm'];
    $user = getUserDetails($conn, $contact);

    if(!$user){
        $msgAdd = "No user found with this contact number!";
    }elseif($password !== $confirm){
        $msgAdd = "Passwords do not match!";
    }else{
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE {$user['table']} SET password=? WHERE contact_number=?");
        $stmt->bind_param("ss",$hashed,$contact);
        if($stmt->execute()){
            $msgAdd = "Password successfully saved!";
        }else{
            $msgAdd = "Failed to save password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Management</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .container {
        width: 500px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        color: #fff;
    }
    h2 { text-align: center; margin-bottom: 30px; }
    .tabs { display: flex; justify-content: center; margin-bottom: 20px; }
    .tab {
        padding: 10px 20px;
        margin: 0 5px;
        cursor: pointer;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }
    .tab.active { background: linear-gradient(135deg,#ff5f6d,#ffc371); color: #fff; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 10px; border: none; outline: none; font-size: 16px; background: rgba(255,255,255,0.2); color: #fff; }
    input::placeholder { color: #e0e0e0; }
    button { width: 100%; padding: 12px; margin-top: 15px; border: none; border-radius: 10px; background: linear-gradient(135deg,#ff5f6d,#ffc371); color: #fff; font-size: 16px; cursor: pointer; transition: all 0.3s ease; }
    button:hover { transform: scale(1.05); }
    .msg { color: #ff6b6b; margin-bottom: 10px; text-align: center; }
    .user-details { margin: 10px 0; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 10px; display: flex; align-items: center; }
    .user-details img { width: 50px; height: 50px; border-radius: 50%; margin-right: 10px; }
</style>
</head>
<body>
<div class="container">
    <h2>Password Management</h2>

    <div class="tabs">
        <div class="tab active" onclick="showTab('add')">Add Password</div>
        <div class="tab" onclick="showTab('forget')">Forget Password</div>
    </div>

    <!-- Add Password Tab -->
    <div class="tab-content active" id="add">
        <?php if($msgAdd) echo "<p class='msg'>$msgAdd</p>"; ?>
        <form method="POST">
            <input type="text" name="add_contact" placeholder="Contact Number" required value="<?php echo isset($contact)?$contact:''; ?>">
            <input type="password" name="add_password" placeholder="Password" required>
            <input type="password" name="add_confirm" placeholder="Confirm Password" required>
            <button type="submit" name="add_save">Save Password</button>
        </form>
    </div>

    <!-- Forget Password Tab -->
    <div class="tab-content" id="forget">
        <?php if($msgForget) echo "<p class='msg'>$msgForget</p>"; ?>
        <form method="POST">
            <input type="text" name="forget_contact" placeholder="Contact Number" required value="<?php echo isset($contact)?$contact:''; ?>">
            <button type="submit" name="forget_check">Check User</button>
        </form>

        <?php if($user){ ?>
        <div class="user-details">
            <img src="<?php echo $user['profile_picture']; ?>" alt="Profile">
            <div>
                <strong><?php echo $user['name']; ?></strong><br>
                NIC: <?php echo $user['nic_number']; ?><br>
                Address: <?php echo $user['address']; ?>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="forget_contact" value="<?php echo $user['contact_number']; ?>">
            <button type="submit" name="forget_clear">Change Password (Clear)</button>
        </form>

        <form method="POST">
            <input type="hidden" name="forget_contact" value="<?php echo $user['contact_number']; ?>">
            <input type="password" name="forget_password" placeholder="New Password" required>
            <input type="password" name="forget_confirm" placeholder="Confirm Password" required>
            <button type="submit" name="forget_save">Save New Password</button>
        </form>
        <?php } ?>
    </div>
</div>

<script>
function showTab(tab){
    document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    document.querySelector('.tab[onclick*="'+tab+'"]').classList.add('active');
    document.getElementById(tab).classList.add('active');
}
</script>
</body>
</html>
