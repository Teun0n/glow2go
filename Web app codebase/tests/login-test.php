<?php
require_once "/opt/lampp/htdocs/Glow2Go/data-controller.php";
// Check if the form was submitted

    // Retrieve and sanitize the form data


    $username = "bob";
    $password = "pragmam";

    $loginForm = [
        'username' => $username,
        'password' =>  $password
    ];

    $success = login($loginForm);

    echo $success;
    if ($success) {
        // Return a success response
        echo "work";
    } else {
        // Return an error response
        echo "didn't work";
    }
?>
