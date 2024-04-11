<?php
include 'connectDB.php'; // Use your existing database connection file

$query = "SELECT COUNT(*) as total_logs FROM users_logs";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['total_logs' => $row['total_logs']]);
} else {
    // Handle error; make sure you also log this server-side
    echo json_encode(['total_logs' => 'Error fetching logs']);
}

$conn->close();
?>
