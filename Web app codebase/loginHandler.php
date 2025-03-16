<?php
require_once "data-controller.php";


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitize the form data


    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginForm = [
        'username' => $username,
        'password' =>  $password
    ];

    $result = json_decode(login($loginForm), true);
    session_start();




    if ($result !== null) {
        // Return a success response
        // Redirect to another page upon successful update
        $_SESSION['username'] = $result['username'];
        $_SESSION['residentId'] = $result['residentId'];
        $_SESSION['loggedIn'] = true;

        header("Location: index.php");

        //echo json_encode(['success' => true, 'message' => 'startime is: '. $startTime]);
        exit; // Ensure that script execution stops after the redirect
    } else {
        $_SESSION['loggedIn'] = false;
        echo json_encode(['success' => false]);

        // Redirect to index.php
        header("Location: login.php");
        exit; // Stop further script execution

        // Return an error response
        //echo json_encode(['success' => false, 'message' => 'Failed to update time. Please try again.']);
    }
}
?>
