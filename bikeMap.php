<?php

$url = "http://maps.googleapis.com/maps/api/staticmap";
$key = "AIzaSyAXX06QD5CdYhhL0SBcuTxqhaMzTXm_FwQ";


$userLat = 51.517633;
$userLng = -0.141718;

$stationLat = 51.51811784;
$stationLng = -0.144228881;

$args = array(
	'key' => $key, 
	//'center' => "{$centerLat},{$centerLng}",
	'markers' => "size:small|{$userLat},{$userLng}|{$stationLat},{$stationLng}",
	//'zoom' => 14,
	'size' => "144x168",
	'sensor' => "false"
);

$mapUrl = $url . "?" . urldecode(http_build_query($args));


$tmp = tempnam("/tmp", "MAP_CONVERT") ;
$tmpIn = "{$tmp}.png";
$tmpOut = "{$tmp}.pbi";

file_put_contents($tmpIn, file_get_contents($mapUrl));
exec("/usr/bin/python pbi.py pbi {$tmpIn} {$tmpOut}");

$pbi = file_get_contents($tmpOut);

unlink($tmp);
unlink($tmpIn);
unlink($tmpOut);

Header("Content-type: application/octet-stream");
echo $pbi;