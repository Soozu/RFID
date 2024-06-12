<?php
header('Content-Type: application/json');
include 'connectDB.php'; // Make sure this path is correct and the file correctly sets up a database connection

$query = "SELECT event_id, event_name AS title, event_start_date AS start, event_end_date AS end FROM calendar_event_master";
$result = $conn->query($query);

$events = array();
while($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
$conn->close();
?>
