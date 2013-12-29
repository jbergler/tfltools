<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateBikeStationsLhrBarclays extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'update:LHR_BARCLAYS';

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
		$url = "http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml";
		$data = file_get_contents($url);

		$BikeShare = BikeShare::find('LHR_BARCLAYS');

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
	                $station = self::parseStation(array_slice($values, $offset, $len));
					

					$entry = $BikeShare->stations()->where('stationId', '=', $station['id'])->get();

					if (count($entry) == 0) {
						$entry = new BikeStation();
						$entry->stationId = $station['id'];
						$entry->name = $station['name'];
						$entry->lat = floatval($station['lat']);
						$entry->lng = floatval($station['long']);
						//$this->comment('Creating station #' . $station['id']);
					}
					else {
						$entry = $entry[0];
						//$this->comment('Updating station #' . $station['id']);
					}

					$entry->available = ($station['installed'] == 'true') && ($station['locked'] == 'false');
					$entry->bikes = intval($station['nbBikes']);
					$entry->emptyDocks = intval($station['nbEmptyDocks']);
					$entry->totalDocks = intval($station['nbDocks']);


					$BikeShare->stations()->save($entry);
	            }
	        } else {
	            continue;
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