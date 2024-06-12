<?php  
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'connectDB.php'; // Connect to the database

// Function to send email
function sendEmail($to, $subject, $message) {
    // Add your email sending code here
    // Example using PHPMailer library:
    require 'vendor/autoload.php';
   
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dashotz14@gmail.com';
    $mail->Password = 'kjax wxfi hbgz wrpl';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->isHTML(false);

    $mail->setFrom('dashotz14@gmail.com', 'Admin Francis');
    $mail->addAddress($to);

    $mail->Subject = $subject;
    $mail->Body = $message;

    if ($mail->send()) {
        echo "Email sent successfully to $to";
    } else {
        echo "Failed to send email to $to. Error: {$mail->ErrorInfo}";
    }
}
// Check if the last log entry has already been emailed
$lastLogQuery = "SELECT * FROM users_logs WHERE email_sent = 0 ORDER BY id DESC LIMIT 1";
$lastLogResult = mysqli_query($conn, $lastLogQuery);

if ($lastLogResult && mysqli_num_rows($lastLogResult) > 0) {
    $lastLog = mysqli_fetch_assoc($lastLogResult);
    $lastLogID = $lastLog['id'];

    // Retrieve the email associated with the card UID
    $cardUID = $lastLog['card_uid'];
    $emailQuery = "SELECT email FROM users WHERE card_uid = '$cardUID'";
    $emailResult = mysqli_query($conn, $emailQuery);

    if ($emailResult && mysqli_num_rows($emailResult) > 0) {
        $emailRow = mysqli_fetch_assoc($emailResult);
        $toEmail = $emailRow['email'];

        // Prepare the email content
        $subject = "Attendance Notification";
        $message = "Dear Parent,\n\nYour child has successfully entered the room.\n\n";
        $message .= "Date: {$lastLog['checkindate']}\n";
        $message .= "Time In: {$lastLog['timein']}\n";
        

        // Send email
        sendEmail($toEmail, $subject, $message);

        // Mark the email as sent in the database
        $updateEmailSentQuery = "UPDATE users_logs SET email_sent = 1 WHERE id = $lastLogID";
        mysqli_query($conn, $updateEmailSentQuery);
    } else {
        echo "Email not found for card UID: $cardUID";
    }
} else {
    echo "No logs found or all logs have been emailed.";
}





?>
<div class="table-responsive" style="max-height: 500px;">
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Student Number/LRN</th>
                <th>Card UID</th>
                <th>Section</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody class="table-secondary">
            <?php
            // Connect to database
            require 'connectDB.php';

            $searchQuery = " ";
            $Start_date = " ";
            $End_date = " ";
            $Start_time = " ";
            $End_time = " ";
            $Card_sel = " ";
            

            if (isset($_POST['log_date'])) {
                // Start date filter
                if (!empty($_POST['date_sel_start'])) {
                    $Start_date = $_POST['date_sel_start'];
                    $_SESSION['searchQuery'] = "checkindate='" . $Start_date . "'";
                } else {
                    $Start_date = date("Y-m-d");
                    $_SESSION['searchQuery'] = "checkindate='" . date("Y-m-d") . "'";
                }
                // End date filter
                if (!empty($_POST['date_sel_end'])) {
                    $End_date = $_POST['date_sel_end'];
                    $_SESSION['searchQuery'] = "checkindate BETWEEN '" . $Start_date . "' AND '" . $End_date . "'";
                }
                // Time-In filter
                if ($_POST['time_sel'] == "Time_in") {
                    // Start time filter
                    if (!empty($_POST['time_sel_start']) && empty($_POST['time_sel_end'])) {
                        $Start_time = $_POST['time_sel_start'];
                        $_SESSION['searchQuery'] .= " AND timein='" . $Start_time . "'";
                    } elseif (!empty($_POST['time_sel_start']) && !empty($_POST['time_sel_end'])) {
                        $Start_time = $_POST['time_sel_start'];
                    }
                    // End time filter
                    if (!empty($_POST['time_sel_end'])) {
                        $End_time = $_POST['time_sel_end'];
                        $_SESSION['searchQuery'] .= " AND timein BETWEEN '" . $Start_time . "' AND '" . $End_time . "'";
                    }
                }
                // Time-out filter
                if ($_POST['time_sel'] == "Time_out") {
                    // Start time filter
                    if (!empty($_POST['time_sel_start']) && empty($_POST['time_sel_end'])) {
                        $Start_time = $_POST['time_sel_start'];
                        $_SESSION['searchQuery'] .= " AND timeout='" . $Start_time . "'";
                    } elseif (!empty($_POST['time_sel_start']) && !empty($_POST['time_sel_end'])) {
                        $Start_time = $_POST['time_sel_start'];
                    }
                    // End time filter
                    if (!empty($_POST['time_sel_end'])) {
                        $End_time = $_POST['time_sel_end'];
                        $_SESSION['searchQuery'] .= " AND timeout BETWEEN '" . $Start_time . "' AND '" . $End_time . "'";
                    }
                }
                // Card filter
                if (!empty($_POST['card_sel'])) {
                    $Card_sel = $_POST['card_sel'];
                    $_SESSION['searchQuery'] .= " AND card_uid='" . $Card_sel . "'";
                }
                // Department filter
                if (!empty($_POST['dev_uid'])) {
                    $dev_uid = $_POST['dev_uid'];
                    $_SESSION['searchQuery'] .= " AND device_uid='" . $dev_uid . "'";
                }
            }

            if (isset($_POST['select_date']) && $_POST['select_date'] == 1) {
                $Start_date = date("Y-m-d");
                $_SESSION['searchQuery'] = "checkindate='" . $Start_date . "'";
            }

            // $sql = "SELECT * FROM users_logs WHERE checkindate=? AND pic_date BETWEEN ? AND ? ORDER BY id ASC";
            $sql = "SELECT * FROM users_logs WHERE " . $_SESSION['searchQuery'] . " ORDER BY id DESC";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo '<p class="error">SQL Error</p>';
            } else {
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
                if (mysqli_num_rows($resultl) > 0) {
                    while ($row = mysqli_fetch_assoc($resultl)) {
                        // Format the date and time
                        $checkindate = date("F d, Y", strtotime($row['checkindate']));
                        $timein = date("g:i A", strtotime($row['timein']));
                        $timeout = date("g:i A", strtotime($row['timeout']));
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['serialnumber']; ?></td>
                            <td><?php echo $row['card_uid']; ?></td>
                            <td><?php echo $row['device_dep']; ?></td>
                            <td><?php echo $checkindate; ?></td>
                            <td><?php echo $timein; ?></td>
                            <td><?php echo $timeout; ?> </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="8">No records found</td></tr>';
                }
                            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Include buzzer control script here if required
?>
