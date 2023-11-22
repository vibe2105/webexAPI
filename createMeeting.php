<?php

$accessToken = 'YOUR_ACCESS_TOKEN';
$roomId = $_POST['roomId'];

$meetingTitle = $_POST['meetingTitle'];
$meetingDate = $_POST['meetingDate'];
$meetingTime = $_POST['meetingTime'];


$startTime = date('c', strtotime("$meetingDate $meetingTime"));
$endTime = date('c', strtotime("$meetingDate $meetingTime +1 hour"));

$webexApiUrl = 'https://api.ciscospark.com/v1/meetings';


$requestBody = json_encode([
    'title' => $meetingTitle,
    'start' => $startTime,
    'end' => $endTime,
    'roomId' => $roomId,
]);

$ch = curl_init($webexApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    echo json_encode(['joinUrl' => $responseData['joinUrl']]);
} else {
    header('HTTP/1.1 ' . $httpCode . ' ' . curl_error($ch));
    echo json_encode(['error' => 'Error creating meeting']);
}
curl_close($ch);

?>