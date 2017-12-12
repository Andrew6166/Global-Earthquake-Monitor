<?php
// The message
$message = file_get_contents('https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/significant_week.geojson');

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
mail('andrewbarker53@gmail.com', 'GEM', $message);
?>