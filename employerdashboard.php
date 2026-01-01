<?php
session_start();
if(!isset($_SESSION['employer'])){
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Initialize variables to prevent undefined warnings
$upload_message = "";
$employer = null;
$profile_pic = "profiles/default.png";

// Fetch employer info if session exists
if(isset($_SESSION['employer'])){
    $employer = $_SESSION['employer'];
    $employer_id = $employer['employer_id'];

    $stmt = $conn->prepare("SELECT * FROM employer_register WHERE employer_id = ?");
    $stmt->bind_param("i", $employer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employer = $result->fetch_assoc();

    // Handle profile picture upload
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['name'] != ""){
        $target_dir = "profiles/";
        if(!is_dir($target_dir)){ mkdir($target_dir, 0755, true); }

        $file_name = basename($_FILES["profile_picture"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif'];

        if(!in_array($file_ext, $allowed_ext)){
            $upload_message = "Only JPG, JPEG, PNG, GIF files allowed.";
        } else {
            $target_file = $target_dir . $employer_id . "_" . time() . "." . $file_ext;
            $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
            if($check !== false){
                if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)){
                    $stmt = $conn->prepare("UPDATE employer_register SET profile_picture = ? WHERE employer_id = ?");
                    $stmt->bind_param("si", $target_file, $employer_id);
                    if($stmt->execute()){
                        $upload_message = "Profile picture updated successfully!";
                        $employer['profile_picture'] = $target_file;
                    } else {
                        $upload_message = "Database update failed!";
                    }
                } else {
                    $upload_message = "Error uploading file.";
                }
            } else {
                $upload_message = "Uploaded file is not an image.";
            }
        }
    }

    // Determine profile picture path
    $profile_pic = !empty($employer['profile_picture']) && file_exists($employer['profile_picture']) 
                    ? $employer['profile_picture'] 
                    : "profiles/default.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employer Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
body, html{height:100%; overflow-x:hidden;}

/* Gradient animated background */
body{
    background: linear-gradient(135deg, #0e1326, #1a1f3a, #0e1326);
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
.container{display:flex;min-height:100vh;transition:all 0.3s;position:relative;z-index:1;}

/* Sidebar */
.sidebar{
    width:220px;background:rgba(31,31,31,0.8);backdrop-filter:blur(10px);
    padding:25px 15px;display:flex;flex-direction:column;transition:0.3s;border-radius:0 20px 20px 0;
}
.sidebar h2{color:var(--primary);text-align:center;margin-bottom:30px;}
.sidebar a{
    color:var(--text);padding:12px 15px;margin-bottom:12px;
    border-radius:12px;display:flex;align-items:center;gap:10px;font-weight:500;transition:0.3s;
    background:rgba(0,0,0,0.2);
}
.sidebar a:hover{background:var(--hover);color:#000;transform:scale(1.05);}

/* Content */
.content{flex:1;padding:30px;overflow-y:auto;position:relative;z-index:1;}
h1,h2,h3{color:var(--primary);margin-bottom:15px;}

/* Glassmorphic profile card */
.profile-card{
    display:flex;flex-wrap:wrap;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border-radius:20px;
    padding:30px;
    box-shadow:0 8px 32px 0 rgba(0, 0, 0, 0.37);
    align-items:center;
    transition:0.3s;
    opacity:0;
    animation: fadeIn 1s forwards;
}
@keyframes fadeIn{to{opacity:1;}}

/* Profile left */
.profile-left{flex:0 0 180px;margin-right:40px;text-align:center;}
.profile-left img{
    width:160px;height:160px;border-radius:50%;
    object-fit:cover;
    border:4px solid var(--primary);
    box-shadow:0 0 20px var(--primary);
    cursor:pointer;
    transition:0.3s;
}
.profile-left img:hover{transform:scale(1.05);box-shadow:0 0 40px var(--primary);}
input[type=file]{display:none;}

/* Profile right */
.profile-right{flex:1;min-width:250px;}
.profile-right .info-row{
    display:flex;align-items:center;margin-bottom:12px;
    padding:10px 15px;background:rgba(255,255,255,0.05);
    border-radius:12px;
    box-shadow:0 0 10px rgba(0,0,0,0.2) inset;
    transition:0.3s;
}
.profile-right .info-row i{color:var(--primary);margin-right:15px;font-size:18px;}
.profile-right .info-row span{font-weight:500;font-size:16px;}
.profile-right .info-row:hover{
    background:rgba(0,212,255,0.1);
    box-shadow:0 0 15px var(--primary)20;
}

/* Messages */
.message{margin:15px 0;padding:12px;border-radius:12px;font-weight:bold;text-align:center;}
.success{background-color:#0ff3a0;color:#003300;}
.error{background-color:#ff4d4d;color:#330000;}
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
        <h2>Employer Menu</h2>
        <a href="?page=profile"><i class="fa fa-user"></i> Profile</a>
        <a href="register.php"><i class="fa fa-check-circle"></i> Registration</a>
        <a href="attendance.php"><i class="fa fa-check-circle"></i> Mark Attendance</a>
        <a href="?page=attendance_info"><i class="fa fa-calendar-check"></i> Attendance Info</a>
        <a href="classfees.php"><i class="fa fa-money-bill"></i> Class Fees</a>
        <a href="?page=classfees_info"><i class="fa fa-money-bill"></i> Class Fees Info</a>
        <a href="teacherfee.php"><i class="fa fa-money-bill"></i> Mark Teacher Fee</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <?php if(isset($_GET['page']) && $_GET['page']=='profile'): ?>
            <h1>My Profile</h1>
            <?php if($upload_message): ?>
                <div class="message <?php echo strpos($upload_message,'successfully')!==false?'success':'error'; ?>">
                    <?php echo htmlspecialchars($upload_message); ?>
                </div>
            <?php endif; ?>

            <?php if($employer): ?>
            <div class="profile-card">
                <div class="profile-left">
                    <form method="POST" enctype="multipart/form-data">
                        <label for="profile_picture">
                            <img src="<?php echo htmlspecialchars($profile_pic); ?>" title="Click to change picture">
                        </label>
                        <input type="file" name="profile_picture" id="profile_picture" onchange="this.form.submit()">
                    </form>
                </div>

                <div class="profile-right">
                    <div class="info-row"><i class="fa fa-id-badge"></i> <span>ID: <?php echo $employer['employer_id'] ?? '-'; ?></span></div>
                    <div class="info-row"><i class="fa fa-user"></i> <span>Name: <?php echo $employer['name'] ?? '-'; ?></span></div>
                    <div class="info-row"><i class="fa fa-phone"></i> <span>Contact: <?php echo $employer['contact_number'] ?? '-'; ?></span></div>
                    <div class="info-row"><i class="fa fa-map-marker-alt"></i> <span>Address: <?php echo $employer['address'] ?? '-'; ?></span></div>
                    <div class="info-row"><i class="fa fa-briefcase"></i> <span>Position: <?php echo $employer['position'] ?? '-'; ?></span></div>
                    <div class="info-row"><i class="fa fa-calendar-alt"></i> <span>Age: <?php echo $employer['age'] ?? '-'; ?></span></div>
                </div>
            </div>
            <?php else: ?>
                <p>Employer data not found.</p>
            <?php endif; ?>

        <?php else: ?>
            <h1>Welcome, <?php echo $employer['name'] ?? "Employer"; ?>!</h1>
            <p>Use the menu to view your profile and update your profile picture.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
