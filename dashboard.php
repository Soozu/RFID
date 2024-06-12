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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
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
            <h5 class="card-title"> Absent Today Total</h5>
            <p class="card-text" id="absent-today-total"><!-- ?php echo $absentToday; ?> -->0</p>
        </div>
    </div>
</div>


<div id='calendar'></div>
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add New Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                <div class="form-group">
        <label for="event_name">Event Name:</label>
        <input type="text" class="form-control" id="event_name">
    </div>
    <div class="form-group">
        <label for="event_start_date">Start Date:</label>
        <input type="date" class="form-control" id="event_start_date">
    </div>
    <div class="form-group">
        <label for="event_end_date">End Date:</label>
        <input type="date" class="form-control" id="event_end_date">
    </div>
    <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_event()">Save Event</button>
            </div>
        </div>
    </div>
</div>
<!-- End popup dialog box -->
 

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        defaultView: 'month',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        selectable: true,
        selectHelper: true,
        eventLimit: true, // allow "more" link when too many events
        events: 'path_to_your_event_fetching_script.php', // Ensure this points to your PHP script
        select: function(start, end) {
            var title = prompt('Event Title:');
            var eventData;
            if (title) {
                eventData = {
                    title: title,
                    start: start,
                    end: end
                };
                $('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
            }
            $('#calendar').fullCalendar('unselect');
        },
        eventClick: function(event, jsEvent, view) {
            alert('Event: ' + event.title);
        }
    });
});

function save_event() {
    var event_name = $("#event_name").val();
    var event_start_date = $("#event_start_date").val();
    var event_end_date = $("#event_end_date").val();

    $.ajax({
        url: "save_event.php", // Points to the PHP script that will handle saving the event to the database
        type: "POST",
        data: {
            event_name: event_name,
            event_start_date: event_start_date,
            event_end_date: event_end_date
        },
        success: function(data) {
            $('#calendar').fullCalendar('refetchEvents'); // This will make the calendar refetch the events from the server
            alert('Event Added Successfully');
            $('#event_entry_modal').modal('hide');
        },
        error: function() {
            alert('There was an error while saving the event!');
        }
    });
}
</script>



<script src="js/dashboard.js"></script>
</body>
</html>
