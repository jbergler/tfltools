<?php

// Localdir
chdir( dirname ( __FILE__ ) );

//Barclays Bike Data;
if (!file_exists("data/livecyclehireupdates.xml") || (time() - filemtime("data/livecyclehireupdates.xml")) > 60 * 5) {
	file_put_contents("data/livecyclehireupdates.xml", file_get_contents("http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml"));
}

//Tube Line Status;
if (!file_exists("data/tube.xml") || (time() - filemtime("data/tube.xml")) > 60) {
	file_put_contents("data/tube.xml", file_get_contents("http://cloud.tfl.gov.uk/TrackerNet/LineStatus"));
}