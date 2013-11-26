<?php

//Barclays Bike Data;

if (!file_exists("data/livecyclehireupdates.xml") || (time() - filemtime("data/livecyclehireupdates.xml")) > 60 * 5) {
	file_put_contents("data/livecyclehireupdates.xml", file_get_contents("http://www.tfl.gov.uk/tfl/syndication/feeds/cycle-hire/livecyclehireupdates.xml"));
}
