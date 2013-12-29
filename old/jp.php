<?php 

$url = "http://jpapi.tfl.gov.uk/api/XML_TRIP_REQUEST2";

$time = new DateTime();

// type_origin = stop
// name_origin = Alexander Road
// name_destination = -0.179349:51.608163:WGS84[DD.ddddd]:71 Fallowcourt Avenue N12
// type_destination = coord

// Docs http://www.tfl.gov.uk/assets/downloads/businessandpartners/journey-planner-api-documentation.pdf.pdf
$request = array(
	'language' => 'en',						// language
	'sessiondId' => 0,						// session id
	'searchLimitMinutes' => 60 * 2 			// search over next 2 hours
	'place_origin' => 'London',				// search bounds
	'itdDateDay' => $time->format("d"),		// day
	'itdDateMonth' => $time->format("m"),	// month
	'itdDateYear' => $time->format("Y"),	// year
	'itdTimeHour' => $time->format("H"),	// hours [0..24]
	'itdTimeMinute' => $time->format("i"),	// minutes
	'itdTripDateTimeDepArr' => 'dep',		// 'arr' or 'dep'
);

// Constants
$transportModes = array(
	0  => 'National Rail',
	1  => 'DLR',
	2  => 'Underground',
	3  => 'Overground',
	4  => 'Tram',
	5  => 'Bus',
	6  => 'Regional Bus', //Not used
	7  => 'Coach',
	8  => 'Emirates Airline',
	9  => 'Boat',
	10 => 'Transit on Demand', //Not used
	11 => 'Replacement Buses',

	// Extra modes, probably dont expose
	100 => 'Walking', 
	101 => 'Bike & Ride', 
	102 => 'Take your bike along',
	107 => 'Bicycle',

	// Extra values, not usable in filters?
	97 => 'Keep sitting (when the line changes number after a break)',
	98 => 'Secure connection',
	99 => 'Foothpath'
);