<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    header("location: login.php");
}

include 'connectDB.php'; // Ensure this path is correct

// Example function to fetch data
function getMonthlyData($sql) {
    global $conn;
    $result = $conn->query($sql);
    $data = array_fill(1, 12, 0);  // Pre-fill the array for all months with zero
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[(int)$row['Month']] = (int)$row['Count'];
        }
    }
    return $data;
}

$enrollmentData = getMonthlyData("SELECT MONTH(user_date) AS Month, COUNT(*) AS Count FROM users GROUP BY MONTH(user_date)");
$presentData = getMonthlyData("SELECT MONTH(checkindate) AS Month, COUNT(*) AS Count FROM users_logs WHERE timeout IS NOT NULL GROUP BY MONTH(checkindate)");
$absentData = getMonthlyData("SELECT MONTH(checkindate) AS Month, COUNT(*) AS Count FROM users_logs WHERE timeout IS NULL GROUP BY MONTH(checkindate)");

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
$sqlAbsentToday = "SELECT COUNT(*) as absent_today FROM users WHERE id NOT IN (SELECT id FROM users_logs WHERE checkindate = '$todayDate')";
$result = $conn->query($sqlAbsentToday);
$absentToday = ($result->num_rows > 0) ? $result->fetch_assoc()['absent_today'] : "No Data";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>


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
                <h5 class="card-title">Absent Today Total</h5>
                <p class="card-text" id="absent-today-total"><?php echo $absentToday; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
<h1 align="center">Chart Monthly</h1>
    <div class="row">

        <div class="col-md-4">
            <canvas id="enrollmentChart"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="presentChart"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="absentChart"></canvas>
        </div>
        <div class="col-md-12" style="max-width: 500px; margin: auto;">
            <h1 align="center">Chart Today</h1>
            <canvas id="AllOverStatusChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

// Console log to check data
console.log("Enrollment Data: ", <?php echo json_encode($enrollmentData); ?>);
console.log("Present Data: ", <?php echo json_encode($presentData); ?>);
console.log("Absent Data: ", <?php echo json_encode($absentData); ?>);

const chartOptions = {
    type: 'bar',
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 60,
                ticks: {
                    stepSize: 10
                }
            }
        }
    }
};

new Chart(document.getElementById('enrollmentChart'), {
    ...chartOptions,
    data: {
        labels: [],
        datasets: [{
            label: 'Monthly Enrollment',
            data: <?php echo json_encode($enrollmentData); ?>,
            borderColor: 'rgb(0, 123, 255)',
            backgroundColor: 'rgb(0, 123, 255)'
        }]
    }
});

new Chart(document.getElementById('presentChart'), {
    ...chartOptions,
    data: {
        labels: [],
        datasets: [{
            label: 'Monthly Present',
            data: <?php echo json_encode($presentData); ?>,
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgb(40, 167, 69)'
        }]
    }
});

new Chart(document.getElementById('absentChart'), {
    ...chartOptions,
    data: {
        labels: [],
        datasets: [{
            label: 'Monthly Absent',
            data: <?php echo json_encode($absentData); ?>,
            borderColor: 'rgb(255, 51, 51)',
            backgroundColor: 'rgb(255, 51, 51)'
        }]
    }
});

// Pie chart for All-Over Status
new Chart(document.getElementById('AllOverStatusChart'), {
    type: 'pie',
    data: {
        labels: ['Enrollment', 'Present', 'Absent'],
        datasets: [{
            label: 'Overall Status',
            data: [<?php echo $enrolledTotal; ?>, <?php echo $presentToday; ?>, <?php echo $absentToday; ?>],
            backgroundColor: [
                'rgb(0, 123, 255)',
                'rgb(40, 167, 69)',
                'rgb(255, 51, 51)'
            ],
            borderColor: [
                'rgb(0, 123, 255)',
                'rgb(40, 167, 69)',
                'rgb(255, 51, 51)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
});
</script>




<script src="js/dashboard.js"></script>
</body>
</html>