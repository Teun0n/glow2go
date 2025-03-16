<?php
require_once "/opt/lampp/htdocs/Glow2Go/data-controller.php";

$postData =  [
        'eventId'=> 23,
        'startTime'=> '2024-05-14 14:00:19.989000',
        'TimeToBathroom' => '2024-05-14 14:05:19.841000',
        'timeInBathroom' => '10:00:19.399000',
        'TimetoBedroom' => '2024-05-14 14:15:19.109000',
        'totalTime' => '00:00:20.000000',
        'alarmed' => 0,
        'residentId' => 1234
];

$timeData = [
    'startTime' => '21:00:00.000000',
    'endTime' => '09:00:00.000000'
];

//$data = updateHours($timeData);
//echo $data;

$data = saveEvent($postData);
echo $data;
?>
