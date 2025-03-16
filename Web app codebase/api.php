<?php
require_once "data-controller.php";

// Check the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];
//$method = 'POST';

if ($method === 'GET' || $method === 'POST') {

    //API for getting
    if ($method === 'GET' && isset($_GET['resource']) ) {

        $resource = $_GET['resource'];

        // Check if the requested resource exists in the sample data
        if ($resource == 'events') {
            // Set response headers to JSON
            header('Content-Type: application/json');

            $ID = (int)$_GET['ID'];

            // Return JSON response for the requested resource
            //echo json_encode($sampleData[$resource]);
            $json = get_events($ID);
            echo $json;
            exit;


        } else if($resource == "active_hours"){
            header('Content-Type: application/json');

            $ID = (int)$_GET['ID'];

            $json = get_active($ID);
            echo ($json);
            exit;


        } else if($resource == "get_latest_event"){
            header('Content-Type: application/json');

            $ID = (int)$_GET['ID'];

            $json = get_latest_event($ID);
            echo ($json);
            exit;
        }else {
            // Handle unsupported or unknown resource
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found']);
            exit;
        }
    } else if ($method === 'POST') {
        // Assuming the request contains JSON data
        $input = file_get_contents('php://input');
        $postData = json_decode($input, true);

        // Validate and process the POST data
        if ($postData && isset($postData['resource']) && isset($postData['data'])) {
        $resource = $postData['resource'];
        $data = $postData['data'];

        //echo json_encode($data);

        if($resource == "event"){
            $reponse = saveEvent($data);
        }

        if($resource == "active_hours"){
            $response = updateHours($data);
        }

        echo json_encode(['message' => 'Data received and processed successfully. ' . $resource]);
        exit;
        } else {
            // Handle invalid or missing POST data
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request. Method: ' . $method]);
            exit;
        }
    }

} else {
    // Handle unsupported request methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed: ' . $method]);
    exit;
}




?>
