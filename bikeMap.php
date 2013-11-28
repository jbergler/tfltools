<?php
require_once('_bikes.php');

$url = "http://maps.googleapis.com/maps/api/staticmap";
$key = "AIzaSyAXX06QD5CdYhhL0SBcuTxqhaMzTXm_FwQ";

// Defaults
$count = 3;
$userLat = 51.539099;
$userLng = -0.141728;

if (isset($_REQUEST['lat']) && isset($_REQUEST['lng'])) {
	$userLat = $_REQUEST['lat'];
	$userLng = $_REQUEST['lng'];
}

if (distance($userLat, $userLng, 51.511214, -0.119824) > 50000) {
	Header("HTTP/1.0 400 Bad Request");
	echo json_encode(array(
		'error' => "Lat/Lng too far out of london.",
	));
	return;
}

$db = readDatabase($file);
$result = array();

if (isset($_REQUEST['stationId']) && isset($db[$_REQUEST['stationId']])) {
	$station = $db[$_REQUEST['stationId']];

	$result['B'] = array('lat' => $station->lat, 'lng' => $station->long);
}
else {
	$maxDistance = 250;
	$num = 1;
	foreach (closest($userLat, $userLng, $db, $count) as $k=>$v) {
		$station = $db[$k];
	    if (intval($v) <= $maxDistance) {
	    	$result[$num++] = array('lat' => $station->lat, 'lng' => $station->long);
	    }
	}
}

$markers = "";
foreach ($result as $k=>$v) {
	$markers .= "&markers=label:{$k}|{$v['lat']},{$v['lng']}";
}

$args = array(
        'key' => $key,
        //'center' => "{$centerLat},{$centerLng}",
        //'zoom' => 14,
        'markers' => "size:mid|{$userLat},{$userLng}&" . $markers,
        'size' => "144x168",
        'sensor' => "false",
        'style' => "visibility:off"
        		.  "&style=feature:road|element:geometry|visibility:on|saturation:-100|color:0x020000|weight:3"
                .  "&style=feature:landscape|element:geometry|visibility:on|color:0xffffff"
);


$mapUrl = $url . "?" . urldecode(http_build_query($args));
$mapData = file_get_contents($mapUrl);

if (isset($_REQUEST['DEBUG'])) {
	header('Content-type: image/png');
	echo $mapData;
	exit(0);
}

$tmp = tempnam("/tmp", "MAP_CONVERT") ;
$tmpIn = "{$tmp}.png";
$tmpOut = "{$tmp}.pbi";

file_put_contents($tmpIn, $mapData);
exec("/usr/bin/python pbi.py pbi {$tmpIn} {$tmpOut}");

$pbi = file_get_contents($tmpOut);

unlink($tmp);
unlink($tmpIn);
unlink($tmpOut);

Header("Content-type: application/octet-stream");
echo $pbi;