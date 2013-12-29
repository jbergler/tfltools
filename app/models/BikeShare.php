<?php

class BikeShare extends Eloquent {
    protected $table = 'bike_shares';

    public function stations() {
        return $this->hasMany('BikeStation');
    }
}