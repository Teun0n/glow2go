<?php
session_start();

    function connect(){
        #connect to server
        $servername = "localhost";
        $username = "cep2";
        $password = "";
        $dbname = "cep2";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;

    }

    function get_events($residentId){
        $conn = connect();

        $sql = "SELECT * FROM Events WHERE `residentId` = $residentId";
        $result = $conn->query($sql);
        $json = array();

        $total_records = mysqli_num_rows($result);

        if($total_records > 0){
            while ($row = $result->fetch_assoc()){
                $json[] = $row;
            }
        }

        $conn->close();

        return json_encode($json);
        //print($json);


        }

    function get_latest_event($residentId){
        $conn = connect();

        $sql = "SELECT * FROM Events WHERE `residentId` = $residentId ORDER BY eventId DESC LIMIT 1";
        $result = $conn->query($sql);
        $json = array();
        $total_records = mysqli_num_rows($result);
        if($total_records == 1){
            $data = $result->fetch_assoc();
            $json[] = $data;
        }

        $conn->close();

        return json_encode($json);

    }

    function get_active($residentId){
        $conn = connect();

        $sql = "SELECT * FROM `Resident` WHERE `residentId` = $residentId";
        $result = $conn->query($sql);
        $json = array();


        $total_records = mysqli_num_rows($result);

        if($total_records == 1){
            $data = $result->fetch_assoc();

            $json = [
                "startTime" => $data["startTime"],
                "endTime" => $data["endTime"]
            ];
        }

        $conn->close();

        return json_encode($json);
        //print($json);
    }

    function saveEvent($data){
        //echo $data;
        $conn = connect();

        $eventId = $data["eventId"];
        $startTime = $data["startTime"];
        $TimeToBathroom = $data["TimeToBathroom"];
        $timeInBathroom = $data["timeInBathroom"];
        $TimetoBedroom = $data["TimetoBedroom"];
        $totalTime = $data["totalTime"];
        $alarmed = $data["alarmed"];
        $residentId = $data["residentId"];


        $sql = "INSERT INTO `Events` (`eventId`, `startTime`, `TimeToBathroom`, `timeInBathroom`, `TimetoBedroom`, `totalTime`, `alarmed`, `residentId`) VALUES ('$eventId', '$startTime', '$TimeToBathroom', '$timeInBathroom', '$TimetoBedroom', '$totalTime', '$alarmed', '$residentId') ";

        $result = $conn->query($sql);
        $conn->close();
        return $result;

    }

    function updateHours($data){
        $conn = connect();

        $residentId = $_SESSION['residentId'];

        //$residentId = 1234;

        $startTime = $data["startTime"];
        $endTime = $data["endTime"];

        $sql = "UPDATE `Resident` SET `startTime` = '14:00:00.000000', `endTime` = '17:00:00.000000' WHERE `Resident`.`residentId` = $residentId;";

        $result = $conn->query($sql);
        if($result == 1){
            return true;
        }else{
            return false;
        }
        $conn->close();
    }

    function login($loginForm){
        $conn = connect();

        $username = $loginForm['username'];
        $password = $loginForm['password'];

        $sql = "SELECT * FROM `Resident` WHERE (username = '$username' AND password = '$password')";

        $result = $conn->query($sql);

        $total_records = mysqli_num_rows($result);

        if($total_records == 1){
            return json_encode($result->fetch_assoc());
        }else{
            return null;
        }


        $conn->close();
    }

?>
