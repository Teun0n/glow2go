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
    <title>Settings</title>
  </head>
  <body>
    <center>
      <H1>Change active hours of the system:</H1>
      <!--<table border="1">
        <tr>
          <th>Start Hour</th>
          <th>End Hour</th>
        </tr>
      </table>-->
      <?php
          require_once "data-controller.php";

          $ID = $_SESSION['residentId'];
          $json = get_active($ID);
          $result = json_decode($json, true);

        ?>
      <form id="settingsForm" action="updateTime.php" method="POST">
      <label for="start">Start Time:</label>
      <input type="time" id="start" name="startTime" value="<?php echo substr($result['startTime'], 0 , -7)?>"><br>
      <!-- The name attribute "startTime" is added -->
      <label for="end">End Time:</label>
      <input type="time" id="end" name="endTime" value="<?php echo substr($result['endTime'], 0 , -7)?>"><br>
      <!-- The name attribute "endTime" is added -->
      <input type="submit" value="Change Active Hours">
    </form>

    </center>


   <script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const startTimeInput = document.getElementById('start');
    const endTimeInput = document.getElementById('end');
    let formChanged = false;

    // Check if the form has been changed
    const checkFormChanged = () => {
      const startTime = startTimeInput.value;
      const endTime = endTimeInput.value;
      const originalStartTime = "<?php echo substr($result['startTime'], 0, -7) ?>";
      const originalEndTime = "<?php echo substr($result['endTime'], 0, -7) ?>";

      if (startTime !== originalStartTime || endTime !== originalEndTime) {
        formChanged = true;
      } else {
        formChanged = false;
      }
    };

    // Set initial state
    checkFormChanged();

    // Add event listeners to inputs
    startTimeInput.addEventListener('input', () => {
      checkFormChanged();
    });

    endTimeInput.addEventListener('input', () => {
      checkFormChanged();
    });

    // Warn before leaving the page if form is changed
    window.addEventListener('beforeunload', (e) => {
      if (formChanged) {
        e.preventDefault();
        e.returnValue = ''; // Required for legacy browsers
        return ''; // Display the default browser prompt
      }
    });

    // Submit form
    form.addEventListener('submit', () => {
      // Reset formChanged flag upon form submission
      formChanged = false;
    });
  });
</script>

  </body>
</html>
