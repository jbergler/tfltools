<?php
require_once('_bikes.php');
header('Content-Type: application/json');

$lat = $_REQUEST['lat'];
$long = $_REQUEST['lng'];

if (!$lat) $lat = 51.517633;
if (!$long) $long = -0.141718;

$count = 5;



if (distance($lat, $lng, 51.511214, -0.119824) > 50000) {
	Header("HTTP/1.0 400 Bad Request");
	echo json_encode(array(
		'error' => "Lat/Lng too far out of london.",
	));
	return;
}


$db = readDatabase($file);
$result = array();

foreach (closest($lat, $long, $db, $count) as $k=>$v) {
	$station = $db[$k];
    $bearing = bearing($lat, $long, $station->lat, $station->long);
    $available = ($station->installed == "true") &&
                 ($station->locked == "false") &&
                 ($station->removalDate == "");
	$result[] = array(
        'id' => $station->id,
		'name' => $station->name,
        'distance'=> $v,
        'bearing' => $bearing,
        'available' => $available,
        'availableBikes' => $station->nbBikes,
        'availableDocks' => $station->nbEmptyDocks,
        'totalDocks' => $station->nbDocks
	);
}

if (count($result) == 0) {
    $result['error'] = "No stations found";
}

echo json_encode($result);
//echo json_encode($result, JSON_PRETTY_PRINT);
