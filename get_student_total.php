<?php
include 'connectDB.php'; // Your database connection file

$sql = "SELECT COUNT(*) as total FROM users"; // Adjust if you have different criteria for 'enrolled'
$result = $conn->query($sql);

if($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['total_students' => $row['total']]);
} else {
    echo json_encode(['total_students' => 'Error']);
}

$conn->close();
?>
