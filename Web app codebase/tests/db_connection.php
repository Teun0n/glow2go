<?php
    function OpenCon()
    {
        $dbhost = "localhost";
        $dbuser = "cep2";
        $dbpass = "pragma"; //or whatever you choose when you installed it
        $db = "cep2";
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $db)
        or die("Connect failed: %s\n". $conn -> error);
        return $conn;
    }
    function CloseCon($conn)
    {
        $conn -> close();
    }
?>
