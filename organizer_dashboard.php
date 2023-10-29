<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $meeting_name = $_POST['meeting_name'];
    $place = $_POST['place'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $sql = "INSERT INTO meetings (organizer_id, name, place, date, start_time, end_time) VALUES ('{$_SESSION['id']}', '$meeting_name', '$place', '$date', '$start_time', '$end_time')";
    if ($conn->query($sql) === TRUE) {
        echo "Meeting created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$sql = "SELECT meetings.id, meetings.name, meetings.date, meetings.start_time, meetings.end_time, users.username AS organizer FROM meetings JOIN users ON meetings.organizer_id = users.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Organizer Dashboard</title>
</head>
<body>
<form action="organizer_dashboard.php" method="post">
    <label>Meeting Name:</label>
    <input type="text" name="meeting_name" required>
    <label>Place:</label>
    <input type="text" name="place" required>
    <label>Date:</label>
    <input type="date" name="date" required>
    <label>Start Time:</label>
    <input type="time" name="start_time" required>
    <label>End Time:</label>
    <input type="time" name="end_time" required>
    <input type="submit" value="Create Meeting">
</form>

<table border="1">
    <thead>
    <tr>
        <th>Participants</th>
        <?php
        $dates = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if (!in_array($row["date"], $dates)) {
                    $dates[] = $row["date"];
                    echo "<th>" . $row["date"] . "</th>";
                }
            }
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $users_sql = "SELECT username, role FROM users";
    $users_result = $conn->query($users_sql);

    while($user_row = $users_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $user_row["username"] . " (" . ucfirst($user_row["role"]) . ")</td>";

        foreach ($dates as $date) {
            $meeting_sql = "SELECT GROUP_CONCAT(CONCAT(start_time, ' - ', end_time) ORDER BY start_time ASC) AS timeslots
                                FROM meetings 
                                JOIN meeting_participants ON meetings.id = meeting_participants.meeting_id
                                JOIN users ON meeting_participants.user_id = users.id
                                WHERE users.username = '" . $user_row["username"] . "' AND meetings.date = '$date'";
            $meeting_result = $conn->query($meeting_sql);
            if ($meeting_result->num_rows > 0) {
                $meeting_data = $meeting_result->fetch_assoc();
                echo "<td>" . str_replace(",", "<br>", $meeting_data["timeslots"]) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        }

        echo "</tr>";
    }
    ?>
    </tbody>
</table>
</body>
</html>