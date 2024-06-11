<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}

include 'connectDB.php'; // Ensure this path is correct

// Fetch student logs total
$sqlLogsTotal = "SELECT COUNT(*) as logs_total FROM users_logs";
$result = $conn->query($sqlLogsTotal);
$logsTotal = ($result->num_rows > 0) ? $result->fetch_assoc()['logs_total'] : "No Data";

// Fetch enrolled students total
$sqlEnrolledTotal = "SELECT COUNT(*) as enrolled_total FROM users";
$result = $conn->query($sqlEnrolledTotal);
$enrolledTotal = ($result->num_rows > 0) ? $result->fetch_assoc()['enrolled_total'] : "No Data";

// Fetch present today total
$todayDate = date("Y-m-d");
$sqlPresentToday = "SELECT COUNT(*) as present_today FROM users_logs WHERE checkindate = '$todayDate' AND timeout IS NOT NULL";
$result = $conn->query($sqlPresentToday);
$presentToday = ($result->num_rows > 0) ? $result->fetch_assoc()['present_today'] : "No Data";

// Fetch absent today total
//sqlAbsentToday = "SELECT COUNT(*) as absent_today FROM users WHERE id NOT IN (SELECT user_id FROM users_logs WHERE checkindate = '$todayDate')";
//$result = $conn->query($sqlAbsentToday);
//$absentToday = ($result->num_rows > 0) ? $result->fetch_assoc()['absent_today'] : "No Data";
    
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include('header.php'); ?>
<h2 align="center">Student Dashboard</h2>
<div class="row">
<div class="col-md-3 mb-4">
    <div class="card text-white bg-info">
        <div class="card-body">
            <i class="fas fa-user-clock"></i> 
            <h5 class="card-title">Student Logs Total</h5>
            <p class="card-text" id="student-logs-total"><?php echo $logsTotal; ?></p>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card text-white bg-primary">
        <div class="card-body">
            <i class="fas fa-users"></i> 
            <h5 class="card-title">Student Enrolled Total</h5>
            <p class="card-text" id="student-enrolled-total"><?php echo $enrolledTotal; ?></p>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card text-white bg-success">
        <div class="card-body">
            <i class="fas fa-user-check"></i> 
            <h5 class="card-title">Present Today Total</h5>
            <p class="card-text" id="present-today-total"><?php echo $presentToday; ?></p>
        </div>
    </div>
</div>

<div class="col-md-3 mb-4">
    <div class="card text-white bg-danger">
        <div class="card-body">
            <i class="fas fa-user-times"></i>
            <h5 class="card-title"> Absent Today Total</h5>
            <p class="card-text" id="absent-today-total"><!-- ?php echo $absentToday; ?> -->0</p>
        </div>
    </div>
</div>



<script src="js/dashboard.js"></script>
</body>
</html>
