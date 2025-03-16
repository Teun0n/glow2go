<?php
require_once "data-controller.php";

// Check the HTTP request method
//$method = $_SERVER['REQUEST_METHOD'];
$method = 'GET';



        $resource = "active_hours";

        // Check if the requested resource exists in the sample data
        if ($resource == 'events') {
            // Set response headers to JSON
            header('Content-Type: application/json');

            // Return JSON response for the requested resource
            //echo json_encode($sampleData[$resource]);
            $json = get_events();
            echo $json;
            exit;


        } else if($resource == "active_hours"){
            header('Content-Type: application/json');

            $json = get_active();
            echo ($json)
            exit;


        } else if($resource == "get_latest_event"){
            header('Content-Type: application/json');

            $json = get_latest_event();
            echo ($json);
            exit;
        }else {
            // Handle unsupported or unknown resource
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found']);
            exit;
        }
