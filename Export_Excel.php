<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Connect to the database
require 'connectDB.php';

// Include the TCPDF library
require 'vendor/autoload.php';

// Set a default value for $Start_date
$Start_date = date("Y-m-d");

// Check if 'To_PDF' is set in the session
if (!isset($_SESSION["To_PDF"])) {
    // Check if 'select_date' is set in the session
    if (isset($_SESSION['select_date'])) {
        $Start_date = $_SESSION['select_date'];
    } else {
        // Handle the case when 'select_date' is not set
        // For example, set a default value or redirect to an error page
        echo 'Please select a date.';
        // Set a default value for 'select_date'
        $_SESSION['select_date'] = $Start_date;
        // You might want to redirect or handle this differently based on your application logic
    }
    
    // Check the database connection
    $conn = mysqli_connect("localhost", "root", "", "rfidattendance");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM users_logs WHERE " . $_SESSION['searchQuery'] . " ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);

    // Check for query execution errors
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if ($result->num_rows > 0) {
        // Create a TCPDF instance
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Your Name');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Attendance Log');
        $pdf->SetSubject('Attendance Log');
        $pdf->SetKeywords('Attendance, Log');

        // Set font
        $pdf->SetFont('helvetica', '', 7);

        // Add a page
        $pdf->AddPage();

        // Add logo and header text
        $logo = 'icons/gov.png'; // Path to your logo image
        $headerText = "GOV D.M. CAMERINO INTEGRATED SCHOOL\nImus CITY\n\n135 Medicion II St, Imus, Cavite\n(046) 489-5114\n\nhttps://www.facebook.com/DepEdTayoGDMCIS107985/\n\n\n\nAttendance Log";

        // Set logo
        $pdf->Image($logo, 12, 18, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Set header text
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetXY(15, 15);
        $pdf->MultiCell(0, 0, $headerText, 0, 'C');

        // Add some space between the header and the table
        $pdf->Ln(10);

        // Define the table structure
        $tableHeader = array('Number', 'Name', 'LRN / Student Number', 'Card UID', 'Teachers UID', 'Section', 'Date log', 'Time In', 'Time Out');

        // Add header row
        $pdf->SetFont('helvetica', 'B', 7);
        $x = 10;
        foreach ($tableHeader as $header) {
            $pdf->Cell(getCellWidth($header), 7, $header, 1, 0, 'C');
        }
        $pdf->Ln();

        // Set font back to regular
        $pdf->SetFont('helvetica', '', 7);

        // Add data rows
        while ($row = $result->fetch_assoc()) {
            // Format the date
            $formattedDate = date("m/d/Y", strtotime($row['checkindate']));
            // Format the time values with minutes and seconds
            $timeInFormatted = date("h:i:s A", strtotime($row['timein']));
            $timeOutFormatted = date("h:i:s A", strtotime($row['timeout']));

            // Add row to the table
            $pdf->Cell(getCellWidth('Number'), 7, $row['id'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('Name'), 7, $row['username'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('LRN / Student Number'), 7, $row['serialnumber'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('Card UID'), 7, $row['card_uid'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('Teachers UID'), 7, $row['device_uid'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('Section'), 7, $row['device_dep'], 1, 0, 'C');
            $pdf->Cell(getCellWidth('Date log'), 7, $formattedDate, 1, 0, 'C');
            $pdf->Cell(getCellWidth('Time In'), 7, $timeInFormatted, 1, 0, 'C');
            $pdf->Cell(getCellWidth('Time Out'), 7, $timeOutFormatted, 1, 1, 'C');
        }

        // Output the PDF file
        $pdfFilePath = 'Attendance_Log_' . $Start_date . '.pdf';
        $pdf->Output($pdfFilePath, 'D');

        // Exit to prevent further output
        exit();
    } else {
        echo "No records found.";
        exit();
    }
}

// If 'To_PDF' is not set in the session, reach this point
echo "Script reached the end.";

// Function to get cell width based on column name
function getCellWidth($columnName) {
    switch ($columnName) {
        case 'Number':
            return 10;
        case 'Name':
            return 30;
        case 'LRN / Student Number':
            return 30;
        case 'Card UID':
            return 20;
        case 'Teachers UID':
            return 25;
        case 'Section':
            return 17;
        case 'Date log':
            return 18;
        case 'Time In':
            return 20;
        case 'Time Out':
            return 20;
        default:
            return 20;
    }
}
?>
