<?php
/*
 * This script can be used to create a complete download by GSD.
 *
 * Usage:
  * php "/path/to/export-by-gsd.php" -w ")&^(D  UIYOY"
 *
*/

// Just in case... six hour timeout
set_time_limit(21600);


error_reporting(E_ALL);
ini_set('display_errors', 1);


// Password required when calling the service
$password = ')&^(D  UIYOY';
$configPath =  dirname(__FILE__) . '/config/settings.ini';

require_once 'DCAExporter.php';
require_once 'includes/library.php';
alwaysFlush();


// Get command line parameters
$options = getopt("w:");
$w = isset($options['w']) ? $options['w'] : false;

// Basic security
if (!$w || $w !== $password) {
    die("You did not say the magic word, bye bye\n\n");
}

// Initialize DCAExporter
$dcaExporter = new DCAExporter();
$dateYmd = date("Y-m-d", strtotime($dcaExporter->getReleaseDate()));

echo "This script creates a DarwinCore Archives for each GSD in the\n";
echo "Species 2000 & ITIS Catalogue of Life: " . $dcaExporter->getReleaseDate() . "\n\n";

foreach ($dcaExporter->getGSDs() as $gsd) {
	
//if ($gsd  != 'FishBase') continue;

	// Fake data submission
	$_POST['gsd'] = $gsd;
	$_POST['block'] = 3;

	// Initialize DCAExporter
	$dcaExporter = new DCAExporter();
	$baseDir = $dcaExporter::basePath();
	$dcaExporter->useIndicator();
	$dcaExporter->setIndicatorBreakLine("\n");
	$dcaExporter->setIndicatorMarkersPerLine(50);
	$dcaExporter->setIndicatorIterationsPerMarker(500);
	
	echo "Writing $gsd data to text files...\n";
	$dcaExporter->createMetaXml();
	$dcaExporter->writeData(false);
	if ($dcaExporter->hasMissingParents()) {
		echo "\nCompleting higher classification...\n";
		$dcaExporter->writeMissingParents();
	}
	echo "\nCompressing to zip archive...\n";
	$dcaExporter->zipArchive();
	echo "\n";
}

echo "Ready!\n\n";


?>