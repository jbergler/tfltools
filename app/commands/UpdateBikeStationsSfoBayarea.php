<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateBikeStationsSfoBayarea extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'update:SFO_BAYAREA';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update data for the London Barclays Bikes';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$url = "http://bayareabikeshare.com/stations/json/";
		$data = file_get_contents($url);

		$BikeShare = BikeShare::find('SFO_BAYAREA');
		if (!$BikeShare) return;

		$bikes = json_decode($data, true); 
		if (isset($bikes['stationBeanList'])) {
			foreach($bikes['stationBeanList'] as $station) {
				$entry = $BikeShare->stations()->where('stationId', '=', $station['id'])->get();

				if (count($entry) == 0) {
					$entry = new BikeStation();
					$entry->stationId = $station['id'];
					$entry->name = $station['stationName'];
					$entry->lat = floatval($station['latitude']);
					$entry->lng = floatval($station['longitude']);
					$this->comment('Creating station #' . $station['id']);
				}
				else {
					$entry = $entry[0];
					$this->comment('Updating station #' . $station['id']);
				}

				$entry->available = ($station['statusKey'] == 1);
				$entry->bikes = intval($station['availableBikes']);
				$entry->totalDocks = intval($station['totalDocks']);
				$entry->emptyDocks = intval($station['availableDocks']);


				$BikeShare->stations()->save($entry);
            }
        }
	}
	
	private static function parseStation($mvalues)  {
	    for ($i=0; $i < count($mvalues); $i++) {
	        $x[$mvalues[$i]["tag"]] = array_key_exists("value", $mvalues[$i]) ? $mvalues[$i]["value"] : null;
	    }
	    return($x);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}