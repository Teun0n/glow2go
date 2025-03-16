<?php
session_start();
?>
<html>
  <head>
    <title>Login</title>
  </head>
  <body>
   <center>
      <img src="pictures/logo-full.png" alt="logo" width=40%>
      <H1>Login:</H1>

      <?php
          require_once "data-controller.php";

        ?>
      <form id="loginForm" action="loginHandler.php" method="POST">
      <label for="username">Username:</label><br>
      <input type="text" id="user" name="username" placeholder="Username"><br>
      <!-- The name attribute "startTime" is added -->
      <label for="password">Password:</label><br>
      <input type="password" id="pass" name="password" placeholder="Password"><br><br>
      <!-- The name attribute "endTime" is added -->
      <input type="submit" value="Login">
    </form>

    </center>

    <?php

// Check if a success message is set in the session
if (isset($_SESSION['loggedIn'])) {
    // Display an alert or message box with the success message
    if(!$_SESSION['loggedIn']){
      $msg = "Username or Password wrong";
      echo "<script type='text/javascript'>alert('$msg');</script>";
    }
    // Unset or clear the session variable to avoid displaying the message again on refresh
    unset($_SESSION['loggedIn']);
}
?>
  </body>
</html>
