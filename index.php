<?php
session_start();

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Detect logged user
$displayName = '';
$dashboardLink = 'login.php';
if (isset($_SESSION['admin'])) {
    $displayName = 'A';
    $dashboardLink = 'admindashboard.php';
} elseif (isset($_SESSION['student'])) {
    $name = $_SESSION['student']['name'] ?? $_SESSION['student']['contact_number'] ?? '';
    $displayName = $name !== '' ? strtoupper(substr($name,0,1)) : 'S';
    $dashboardLink = 'studentdashboard.php';
} elseif (isset($_SESSION['teacher'])) {
    $name = $_SESSION['teacher']['name'] ?? $_SESSION['teacher']['contact_number'] ?? '';
    $displayName = $name !== '' ? strtoupper(substr($name,0,1)) : 'T';
    $dashboardLink = 'teacherdashboard.php';
} elseif (isset($_SESSION['employer'])) {
    $name = $_SESSION['employer']['name'] ?? $_SESSION['employer']['contact_number'] ?? '';
    $displayName = $name !== '' ? strtoupper(substr($name,0,1)) : 'E';
    $dashboardLink = 'employerdashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Anonymous Education Institute</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet"/>
<style>
/* === BODY & GENERAL === */
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:linear-gradient(to right, #2c5364,#203a43,#0f2027);
    overflow-x:hidden;
    position:relative;
    color:#fff;
}

/* Floating shapes */
.floating-shape{
    position:absolute;
    border-radius:50%;
    opacity:0.6;
    animation: float 10s infinite alternate;
    z-index:0;
}
.shape1{width:120px;height:120px;background:#ff5f6d;top:10%;left:5%;animation-duration:12s;}
.shape2{width:80px;height:80px;background:#ffc371;top:20%;right:10%;animation-duration:9s;}
.shape3{width:150px;height:150px;background:#24c6dc;bottom:15%;left:15%;animation-duration:14s;}
.shape4{width:60px;height:60px;background:#ff5f6d;bottom:20%;right:20%;animation-duration:11s;}
@keyframes float{
    0%{transform:translateY(0px) rotate(0deg);}
    50%{transform:translateY(-20px) rotate(180deg);}
    100%{transform:translateY(0px) rotate(360deg);}
}

/* === NAVBAR === */
.navbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    padding:15px 50px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(12px);
    border-bottom:1px solid rgba(255,255,255,0.2);
    z-index:10;
}
.nav-logo{color:#fff;font-size:24px;font-weight:700;}
.nav-right{display:flex;gap:15px;align-items:center;}
.nav-btn{
    padding:10px 24px;
    background:linear-gradient(135deg,#ff5f6d,#ffc371);
    color:#2c3e50;
    text-decoration:none;
    border-radius:50px;
    font-weight:600;
    font-size:14px;
    transition:all 0.3s ease;
}
.nav-btn:hover{transform:scale(1.05);box-shadow:0 4px 15px rgba(255,147,106,0.6);}
.nav-circle{
    width:42px;height:42px;border-radius:50%;
    background:rgba(255,255,255,0.2);
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:16px;font-weight:700;
    cursor:pointer;
    transition:all 0.3s ease;
}
.nav-circle:hover{background:linear-gradient(135deg,#ff5f6d,#ffc371);color:#2c3e50;}

/* === HERO === */
.hero{
    height:100vh;
    position:relative;
    display:flex;
    justify-content:flex-start;
    align-items:center;
    padding-left:10%;
    overflow:hidden;
    /* Add background image with gradient overlay */
    background: 
        linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
        url('hero-bg.jpg') center/cover no-repeat;
}
.hero-content{
    position:relative;
    z-index:2;
    max-width:600px;
    color:#fff;
    animation: fadeInUp 1s ease forwards;
    opacity:0;
}
.hero-content h1{font-size:48px;margin-bottom:20px;}
.hero-content p{font-size:18px;margin-bottom:30px;}
.hero-content a{
    display:inline-block;
    padding:14px 36px;
    border-radius:50px;
    background:linear-gradient(135deg,#ff5f6d,#ffc371);
    color:#2c3e50;
    text-decoration:none;
    font-weight:700;
    transition:all 0.3s ease;
}
.hero-content a:hover{transform:scale(1.05);box-shadow:0 8px 20px rgba(255,147,106,0.5);}

/* Fade in animation */
@keyframes fadeInUp{
    0%{opacity:0;transform:translateY(-20px);}
    100%{opacity:1;transform:translateY(0);}
}

/* === ABOUT SECTION === */
.about-section{
    padding:80px 40px;
    max-width:1200px;
    margin:auto;
    display:flex;
    gap:40px;
    flex-wrap:wrap;
    align-items:center;
}
.about-text{
    flex:1 1 500px;
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:40px;
    color:#fff;
    animation: fadeInUp 1s ease forwards;
}
.about-text h2{font-size:36px;margin-bottom:20px;color:#ffc371;}
.about-text p{font-size:16px;line-height:1.8;}
.about-cards{
    flex:1 1 500px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}
.card{
    background:rgba(255,255,255,0.1);
    border-radius:20px;
    padding:30px 20px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.25);
    transition:transform 0.3s,box-shadow 0.3s;
}
.card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 25px rgba(0,0,0,0.35);
}
.card h3{font-size:20px;margin-bottom:10px;color:#ffc371;}
.card p{font-size:14px;line-height:1.5;}
.card-icon{font-size:36px;color:#ff5f6d;margin-bottom:15px;}

/* === TEACHERS SECTION === */
.teachers-section{padding:80px 40px;max-width:1200px;margin:auto;color:#fff;}
.teachers-title{text-align:center;font-size:40px;margin-bottom:40px;color:#ffc371;}
.teachers-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:30px;}

/* Flip cards */
.flip-card{
    background-color:transparent;
    width:100%;
    height:320px;
    perspective:1000px;
    cursor:pointer;
    display:none;
}
.flip-card.show-card{display:block; animation:fadeInUp 0.8s ease forwards;}
.flip-card-inner{
    position:relative;
    width:100%;
    height:100%;
    transition:transform 0.8s;
    transform-style:preserve-3d;
}
.flip-card:hover .flip-card-inner{transform:rotateY(180deg);}
.flip-card-front, .flip-card-back{
    position:absolute;width:100%;height:100%;
    -webkit-backface-visibility:hidden;
    backface-visibility:hidden;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.4);
}
.flip-card-front img{width:100%;height:100%;object-fit:cover;}
.flip-card-back{
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(10px);
    transform:rotateY(180deg);
    padding:20px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    text-align:center;
}
.flip-card-back h3{font-size:22px;margin-bottom:10px;color:#ffc371;}
.flip-card-back p{font-size:14px;margin:6px 0;}
#showMoreBtn{
    display:inline-block;
    margin-top:20px;
    padding:12px 28px;
    font-size:16px;
    border:none;
    border-radius:8px;
    background:linear-gradient(135deg,#ff5f6d,#ffc371);
    color:#2c3e50;
    cursor:pointer;
    transition:all 0.3s ease;
}
#showMoreBtn:hover{transform:scale(1.05);box-shadow:0 8px 20px rgba(255,147,106,0.5);}

/* RESPONSIVE */
@media(max-width:768px){
    .hero-content h1{font-size:36px;}
    .about-section{flex-direction:column;}
    .about-cards{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<!-- Floating shapes -->
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>
<div class="floating-shape shape3"></div>
<div class="floating-shape shape4"></div>

<!-- Navbar -->
<div class="navbar">
  <div class="nav-logo">Anonymous Education Institute</div>
  <div class="nav-right">
    <?php if($displayName !== ''): ?>
        <a class="nav-circle" href="<?php echo htmlspecialchars($dashboardLink, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></a>
        <a class="nav-btn logout-btn" href="?logout=1">Logout</a>
    <?php else: ?>
        <a class="nav-btn login-btn" href="login.php">Login</a>
    <?php endif; ?>
  </div>
</div>

<!-- Hero Section -->
<div class="hero">
  <div class="hero-content">
    <h1>Welcome to Anonymous Education Institute</h1>
    <p>Learn, teach, and grow with us. Unlock your potential with our wide range of courses!</p>
    <a href="login.php">Get Started</a>
  </div>
</div>

<!-- About Section -->
<div class="about-section">
  <div class="about-text">
    <h2>About Us</h2>
    <p>We provide a platform for learning, teaching, and personal growth. Our institute helps students, teachers, and professionals explore knowledge and achieve their goals efficiently.</p>
  </div>
  <div class="about-cards">
    <div class="card">
      <div class="card-icon">🎓</div>
      <h3>Expert Teachers</h3>
      <p>Learn from highly qualified and experienced teachers dedicated to your success.</p>
    </div>
    <div class="card">
      <div class="card-icon">📚</div>
      <h3>Quality Courses</h3>
      <p>Access a wide range of courses covering academic, professional, and personal development.</p>
    </div>
    <div class="card">
      <div class="card-icon">🌟</div>
      <h3>Achievements</h3>
      <p>Track your progress and achieve certificates that help showcase your skills.</p>
    </div>
    <div class="card">
      <div class="card-icon">💻</div>
      <h3>Online Access</h3>
      <p>Learn anytime, anywhere with our user-friendly online platform and dashboards.</p>
    </div>
  </div>
</div>

<!-- Teachers Section -->
<?php
require_once "db_connect.php";
$query = "SELECT teacher_id, name, teach_subject, contact_number, subject_stream, profile_picture FROM teacher_register";
$result = $conn->query($query);
?>
<div class="teachers-section">
  <h2 class="teachers-title">Our Teachers</h2>
  <div class="teachers-grid">
      <?php
      $counter = 0;
      while($row = $result->fetch_assoc()):
          $img = (!empty($row['profile_picture']) && file_exists($row['profile_picture'])) ? $row['profile_picture'] : "profiles/default.png";
          $showClass = ($counter < 8) ? "show-card" : ""; // first 8
      ?>
      <div class="flip-card <?php echo $showClass; ?>" onclick="window.location.href='timetable.php?teacher_id=<?php echo $row['teacher_id']; ?>'">
          <div class="flip-card-inner">
              <div class="flip-card-front">
                  <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" />
              </div>
              <div class="flip-card-back">
                  <h3><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['teach_subject'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><strong>Stream:</strong> <?php echo htmlspecialchars($row['subject_stream'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_number'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
          </div>
      </div>
      <?php $counter++; endwhile; ?>
  </div>
  <div style="text-align:center;">
      <button id="showMoreBtn">Show More</button>
  </div>
</div>

<script>
document.getElementById('showMoreBtn').addEventListener('click', function(){
    const hiddenCards = document.querySelectorAll('.teachers-grid .flip-card:not(.show-card)');
    hiddenCards.forEach(card => card.classList.add('show-card'));
    this.style.display = 'none';
});
</script>

</body>
</html>
