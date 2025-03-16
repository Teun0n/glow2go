<?php
require_once '/opt/lampp/htdocs/Glow2Go/data-controller.php'
// Define API endpoint URL
#$apiUrl = 'http://localhost/Glow2Go/api.php';

// Sample POST data in JSON format
$postData = [
    'resource' => 'event',
    'data'=> [
        'eventId' => 21,
        'startTime' => '2024-06-15 14:00:19.989000',
        'TimeToBathroom' => '2024-06-15 14:05:19.841000',
        'timeInBathroom' => '10:00:19.399000',
        'TimetoBedroom' => '2024-06-15 14:15:19.109000',
        'totalTime' => '00:00:20.000000',
        'alarmed' => 0,
        'residentId' => 1234 ],
];

/*// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

// Execute cURL session
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'API Response: ' . $response;
}

// Close cURL session
curl_close($ch);*/

saveEvent($postData);

?>
