<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["meeting_id"])) {
    $meeting_id = $_POST["meeting_id"];
    $user_id = $_SESSION["id"];

    $check_sql = "SELECT * FROM meeting_participants WHERE meeting_id = $meeting_id AND user_id = $user_id";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows == 0) {
        $join_sql = "INSERT INTO meeting_participants (meeting_id, user_id) VALUES ($meeting_id, $user_id)";
        if ($conn->query($join_sql) === TRUE) {
            echo "Successfully joined the meeting!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "You are already assigned to this meeting!";
    }
}

$sql = "SELECT * FROM meetings";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Participant Dashboard</title>
</head>
<body>
<table border="1">
    <thead>
    <tr>
        <th>Meeting Name</th>
        <th>Date</th>
        <th>Time-slot</th>
        <th>Organizer</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["date"] . "</td>";
            echo "<td>" . $row["start_time"] . " - " . $row["end_time"] . "</td>";

            $organizer_sql = "SELECT username FROM users WHERE id = " . $row["organizer_id"];
            $organizer_result = $conn->query($organizer_sql);
            $organizer_data = $organizer_result->fetch_assoc();
            echo "<td>" . $organizer_data["username"] . "</td>";

            echo "<td>";
            echo "<form action='participant_dashboard.php' method='post'>";
            echo "<input type='hidden' name='meeting_id' value='" . $row["id"] . "'>";
            echo "<input type='submit' value='Join'>";
            echo "</form>";
            echo "</td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No meetings available.</td></tr>";
    }
    ?>
    </tbody>
</table>
</body>
</html>