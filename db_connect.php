<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doodle_clone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//example database
//   username      password            email                 role
// 'organizer', 'organizer123', 'organizer@example.com', 'organizer'
// 'participant1', 'participant123', 'participant1@example.com', 'participant'
// 'participant2', 'participant123', 'participant2@example.com', 'participant'
?>