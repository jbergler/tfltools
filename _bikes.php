<?php

$file = "data/livecyclehireupdates.xml";

if (!file_exists($file)) {
	Header("HTTP/1.0 503 Service Unavailable");
	echo json_encode(array(
		'error' => "Data currently not available.",
	));
	return;
}


class Station implements JsonSerializable {
	// <station>
	// 	<id>1</id>
	// 	<name>River Street , Clerkenwell</name>
	// 	<terminalName>001023</terminalName>
	// 	<lat>51.52916347</lat>
	// 	<long>-0.109970527</long>
	// 	<installed>true</installed>
	// 	<locked>false</locked>
	// 	<installDate>1278947280000</installDate>
	// 	<removalDate/>
	// 	<temporary>false</temporary>
	// 	<nbBikes>13</nbBikes>
	// 	<nbEmptyDocks>6</nbEmptyDocks>
	// 	<nbDocks>19</nbDocks>
	// </station>

    private $data = array();

    function Station ($values) 
    {
        foreach ($values as $k=>$v)
            $this->data[$k] = trim($v);
    }

    function __get($k) {
    	return $this->data[$k];
    }

    function __set($k, $v) {
    	return $this->data[$k] = $v;
    }

	public function jsonSerialize() {
        return $this->data;
    }
}

function readDatabase($filename) 
{
    // read the XML database
    $data = implode("", file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    // loop through the structures
    foreach ($tags as $key=>$val) {
        if ($key == "station") {
            $ranges = $val;
            for ($i=0; $i < count($ranges); $i+=2) {
                $offset = $ranges[$i] + 1;
                $len = $ranges[$i + 1] - $offset;
                $station = parseStation(array_slice($values, $offset, $len));
                $tdb[$station->id] = $station; 
            }
        } else {
            continue;
        }
    }
    return $tdb;
}

function parseStation($mvalues)  
{
    for ($i=0; $i < count($mvalues); $i++) {
        $x[$mvalues[$i]["tag"]] = array_key_exists("value", $mvalues[$i]) ? $mvalues[$i]["value"] : null;
    }
    return new Station($x);
}

function distance($lat1, $lon1, $lat2, $lon2)
{
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $meters = abs(round($miles * 1.609344 * 1000));
    return max(5, $meters);
}

function closest($lat, $lng, $data, $count = 3) {
	$distances = array_map(function($item) use($lat, $lng) {
	    return distance($item->lat, $item->lng, $lat, $lng);
	}, $data);

	asort($distances);
	return array_slice($distances, 0, $count, true);
}

function bearing($lat1, $lon1, $lat2, $lon2) {
    $bearing = (rad2deg(atan2(sin(deg2rad($lon2) - deg2rad($lon1)) * cos(deg2rad($lat2)), cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon2) - deg2rad($lon1)))) + 360) % 360;
    $tmp = round($bearing / 22.5);
    $rose = array(0=>"N",1=>"NNE",2=>"NE",3=>"ENE",4=>"E",5=>"ESE",6=>"SE",7=>"SSE",8=>"S",9=>"SSW",10=>"SW",11=>"WSW",12=>"W",13=>"WNW",14=>"NW",15=>"NNW");
    return $rose[$tmp];
}

