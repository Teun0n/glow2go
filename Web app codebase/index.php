<?php
session_start(); // Start or resume a session
if($_SESSION["loggedIn"] == false){
  //not logged in go to log in page.
  header("Location: login.php");
  exit;
}
?>
<html>
<head>
  <title>Index</title>
  <style>
    .button-container {
      width: 700px;
      background: gray;
      overflow-y: auto;
    }

    .button-container > a {
      width: 200px;
      height: 200px;
      float: left;
      background: url('pictures/button.png') no-repeat center top lightgray;
      margin: 15px;
    }
  </style>
</head>
<body>
<?php

// Check if a success message is set in the session
if (isset($_SESSION['success_message'])) {
    // Display an alert or message box with the success message
    $msg = $_SESSION["success_message"];
    echo "<script type='text/javascript'>alert('$msg');</script>";


    // Unset or clear the session variable to avoid displaying the message again on refresh
    unset($_SESSION['success_message']);
}
?>

<center>
  <div class="menu-container">
    <div class="button-container">
      <a href="trends.php" class="button">Trends</a>
      <a href="events.php" class="button">Events</a>
      <a href="settings.php" class="button">Settings</a>
    </div>
  </div>
</center>

</body>
</html>
