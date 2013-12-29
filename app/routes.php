<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('/bikes/closest', function() {
    $lat = 51.523266;
    $lng = -0.124583;

    if (Input::has('lat') && Input::has('lng')) {
        $lat = Input::get('lat');
        $lng = Input::get('lng');
    }

    $stations = BikeStation::closest($lat, $lng, 5)->get();

    $result = array();
    foreach ($stations as $station) {
        $result[] = array(
            'id' => $station->id,
            'name' => $station->name,
            'distance'=> $station->distance,
            'bearing' => bearing($lat, $lng, $station->lat, $station->lng),
            'available' => $station->available ? true : false,
            'bikes' => $station->bikes,
            'docks' => $station->emptyDocks,
            'totalDocks' => $station->totalDocks,
        );
    }

    return Response::json($result);
});

function bearing($lat1, $lon1, $lat2, $lon2) {
    $bearing = (rad2deg(atan2(sin(deg2rad($lon2) - deg2rad($lon1)) * cos(deg2rad($lat2)), cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon2) - deg2rad($lon1)))) + 360) % 360;
    $tmp = round($bearing / 22.5);
    $rose = array(0=>"N",1=>"NNE",2=>"NE",3=>"ENE",4=>"E",5=>"ESE",6=>"SE",7=>"SSE",8=>"S",9=>"SSW",10=>"SW",11=>"WSW",12=>"W",13=>"WNW",14=>"NW",15=>"NNW");
    return $rose[$tmp];
}
