<?php
    $servername = "localhost";
    $username = "cep2";
    $password = "pragma";
    $dbname = "cep2";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM helloworld";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    // output data of each row
        while($row = $result->fetch_assoc()) {
        echo "The user " . $row["whoSaidHello"]. " - said: " .
        $row["whatDidTheySay"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    $conn->close();
?>
