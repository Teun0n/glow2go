<?php
    include 'db_connection.php';
    //echo "Connecting";
    $conn = OpenCon();
    echo "Connected Successfully";
    CloseCon($conn);
?>
