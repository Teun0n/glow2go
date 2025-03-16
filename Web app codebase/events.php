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
    <title>Events page</title>
</head>
<style>
tr:nth-child(even) {
  background-color: lightgray;

}
th{
  background-color: gray;
  padding-left: 20px;
  padding-right: 20px;
  padding-bottom: 5px;
}
td{
  padding-left: 2px;
  padding-right: 20px;
}

.bg-alarmed {
    background-color: red;
}
</style>
<body>

    <h2>Events:</h2>

    <center>
    <table border="1">
        <?php

            require_once "data-controller.php";
            $residentId = $_SESSION['residentId'];
            $response = get_events($residentId);

            $data = null;
            if ($response !== false) {
                $data = json_decode($response, true); // Convert JSON string to associative array
                //print_r($data); // Output response data
            } else {
                echo 'Error fetching data from API';
            }


            $rows = count($data);
            $cols = 6;
            echo "<tr>";
            echo "<th>Event nr</th>";
            echo "<th>Start</th>";
            echo "<th>Time to reach bathroom</th>";
            echo "<th>Time in bathrooom</th>";
            echo "<th>Time to reach bedroom</th>";
            echo "<th>Total time</th>";
            echo "</tr>";
            // Loop through each row
            for ($i = 0; $i < $rows; $i++) {

                if(array_values(array_values($data)[$i])[6] == 1){
                    echo "<tr style=\"background-color:red\">";
                }else{
                    echo "<tr>";
                }


                // Loop through each column
                for ($j = 0; $j < $cols; $j++) {
                    $cell = array_values(array_values($data)[$i])[$j];
                    if($cell === '1971-01-01 00:00:01.000000' || $cell === '00:00:00.000000'){
                        $cell = 'N/A';
                    }
                    if($cell[4] === '-' && $cell[7] === '-'){
                        $cell = substr($cell, 0, -7);
                    }
                    if($cell[2] === ':' && $cell[5] === ':'){
                        $cell = substr($cell, 0, -7);
                    }


                    echo "<td>$cell</td>";
                }

                echo "</tr>";
            }
        ?>
    </table>
    </center>

</body>
</html>
