<?php
header('Content-Type: application/json');


$file = "data/tube.xml";

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
        if ($key == "LineStatus") {
            $ranges = $val;
            for ($i=0; $i < count($ranges); $i+=2) {
                $offset = $ranges[$i] + 1;
                $len = $ranges[$i + 1] - $offset;
                print_r($values);
                $tdb[] = parseStation(array_slice($values, $offset, $len));
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

$result = simplexml_load_file($file);

if (count($result) == 0) {
    $result['error'] = "No stations found";
}

// echo json_encode($result);
echo json_encode($result, JSON_PRETTY_PRINT);
