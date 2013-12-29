<?php

class BikeStation extends Eloquent {
    protected $table = 'bike_stations';

    public function bikeShare() {
        return $this->belongsTo('BikeShare', 'bike_share');
    }

    public static function closest($lat, $lng, $count = 5) {
        $lat = floatval($lat);
        $lng = floatval($lng);

        return self::select('*')
            ->select(DB::raw("*, " 
                . "ROUND(6371 * 1000 * acos("
                    . "cos(radians({$lat}))"
                    . "* cos(radians(lat))"
                    . "* cos(radians(lng) - radians({$lng}))"
                    . "+ sin(radians({$lat}))"
                    . "* sin(radians(lat))"
                . ")) AS distance")
            )
            //->having('distance', '<', 25)
            ->orderBy('distance')
            ->limit($count);
    }
}
/*
(
    3959 * acos (
      cos ( radians(78.3232) )
      * cos( radians( lat ) )
      * cos( radians( lng ) - radians(65.3234) )
      + sin ( radians(65.3234) )
      * sin( radians( lat ) )
    )
  )
  */