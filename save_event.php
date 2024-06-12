<?php
// Include your database connection details
include('connectDB.php');

// Collect data from the form
$eventName = $_POST['event_name'];
$eventStartDate = $_POST['event_start_date'];
$eventEndDate = $_POST['event_end_date'];

// SQL to insert the new event
$sql = "INSERT INTO calendar_event_master (event_name, event_start_date, event_end_date) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $eventName, $eventStartDate, $eventEndDate);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $response = ['status' => true, 'msg' => 'Event saved successfully!'];
} else {
    $response = ['status' => false, 'msg' => 'Failed to save event.'];
}
$stmt->close();
$conn->close();

echo json_encode($response);
?>
