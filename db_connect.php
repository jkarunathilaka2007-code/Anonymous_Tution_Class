<?php
$servername = "localhost";
$username = "root";     // WAMP default username
$password = "";         // WAMP default password
$dbname = "anonymous_db"; // Replace with your DB name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
