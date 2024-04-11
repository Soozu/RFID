<?php
// Connect to the database
require 'connectDB.php';

$output = '';

if (isset($_SESSION["To_Excel"])) {

    $searchQuery = " ";
    $Start_date = " ";
    $End_date = " ";
    $Start_time = " ";
    $End_time = " ";
    $card_sel = " ";

    // Rest of your code...

    $sql = "SELECT * FROM users_logs WHERE " . $_SESSION['searchQuery'] . " ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $output .= '<table class="table" bordered="1">  
                    <TR>
                      <TH>ID</TH>
                      <TH>Name</TH>
                      <TH>Serial Number</TH>
                      <TH>Card UID</TH>
                      <TH>Device ID</TH>
                      <TH>Device Dep</TH>
                      <TH>Date log</TH>
                      <TH>Time In</TH>
                      <TH>Time Out</TH>
                    </TR>';

        while ($row = $result->fetch_assoc()) {
            $output .= '<TR> 
                            <TD> ' . $row['id'] . '</TD>
                            <TD> ' . $row['username'] . '</TD>
                            <TD> ' . $row['serialnumber'] . '</TD>
                            <TD> ' . $row['card_uid'] . '</TD>
                            <TD> ' . $row['device_uid'] . '</TD>
                            <TD> ' . $row['device_dep'] . '</TD>
                            <TD> ' . $row['checkindate'] . '</TD>
                            <TD> ' . $row['timein'] . '</TD>
                            <TD> ' . $row['timeout'] . '</TD>
                        </TR>';
        }
        $output .= '</table>';

        // Send headers
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=User_Log' . $Start_date . '.xls');

        // Output the content
        echo $output;
        exit();
    } else {
        header("location: UsersLog.php");
        exit();
    }
}
?>