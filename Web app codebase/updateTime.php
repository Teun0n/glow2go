<?php
require_once "data-controller.php";
session_start();


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data


    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];


    $timeData = [
        'startTime' => $startTime . "000000",
        'endTime' =>  $endTime . "000000"
    ];

    $success = updateHours($timeData);
    session_start();

    if ($success) {
        // Return a success response
        // Redirect to another page upon successful update
        $_SESSION['success_message'] = 'Time updated successfully!';

        header("Location: index.php");

        //echo json_encode(['success' => true, 'message' => 'startime is: '. $startTime]);
        exit; // Ensure that script execution stops after the redirect
    } else {
        // Set a session variable with error message (if needed)
        $_SESSION['error_message'] = 'Failed to update time. Please try again.';

        // Redirect to index.php
        header("Location: index.php");
        exit; // Stop further script execution

        // Return an error response
        //echo json_encode(['success' => false, 'message' => 'Failed to update time. Please try again.']);
    }
}
?>
