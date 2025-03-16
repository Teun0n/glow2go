<?php
require_once "data-controller.php";
// Check if the form was submitted

    // Retrieve and sanitize the form data


    $startTime = "14:00:00";
    $endTime = "13:00:00";

    $timeData = [
        'startTime' => $startTime . "000000",
        'endTime' =>  $endTime . "000000"
    ];

    $success = updateHours($timeData);

    echo $success;
    if ($success) {
        // Return a success response
        echo "work";
    } else {
        // Return an error response
        echo "didn't work";
    }
?>
