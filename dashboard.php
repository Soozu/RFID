<?php
include 'header.php'; 
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Arduino Dashboard</title>
    <!-- Include your CSS files here -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- Cards container -->
    <div class="row">
        <!-- Student Logs Total -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Student Logs Total</h5>
                    <p class="card-text" id="student-logs-total"></p>
                </div>
            </div>
        </div>

        <!-- Student Enrolled Total -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Student Enrolled Total</h5>
                    <p class="card-text" id="student-enrolled-total"></p> <!-- Dynamic content -->
                </div>
            </div>
        </div>

        <!-- Present Today Total -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Present Today Total</h5>
                    <p class="card-text" id="present-today-total">No Data In database</p> <!-- Dynamic content -->
                </div>
            </div>
        </div>

        <!-- Absent Today Total -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Absent Today Total</h5>
                    <p class="card-text" id="absent-today-total">No Data In database</p> <!-- Dynamic content -->
                </div>
            </div>
        </div>
    </div>

    <!-- Additional sections of the dashboard -->

</div>

    <!-- Include your scripts here -->
    <script src="js/dashboard.js"></script>
</body>
</html>