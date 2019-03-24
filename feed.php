<?php

$inReachFeedId = "";                                            // Put your InReach share ID here
$startDate = "2019-01-01T00:00Z";                               // Start date of your expedition
$endDate = "2019-12-31T00:00Z";                                 // End date of your expedition
$cacheTime = 600;                                               // Minimum interval for polling the Garmin server [seconds]

$feedUrl = "https://eur.inreach.garmin.com/Feed/Share/";        // Don't change this unless Garmin change their setup
$filePath = "feed.kml";                                         // Don't need to change this
$tempPath = "temp.kml";                                         // Or this

// Check if we already have a recent copy of the feed on disk
if (!file_exists($filePath) || time() - filemtime($filePath) > $cacheTime)
{
    // Touch the file so that other clients don't trigger a refresh for 10s
    touch($filePath, time() - $cacheTime + 10);
    
    // Try to update the feed
    $url = $feedUrl . $inReachFeedId . "?d1=" . $startDate . "&d2=" . $endDate;
    if (file_put_contents( $tempPath, fopen($url, 'r'), LOCK_EX) > 0)
    {
        // Move the downloaded file so it can be served
        rename($tempPath, $filePath);
    }
}

if (!file_exists($filePath))
{
    die("Error: File not found.");
}

// Serve the cached file
header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
header("Cache-Control: public"); // needed for internet explorer
header("Content-Type: application/vnd.google-earth.kml+xml");
readfile($filePath);
die();

?>