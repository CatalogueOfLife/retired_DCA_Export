<?php
/*
 * This script can be used to create a complete download from the command line.
 * It replicates the manual steps required to create the export and replace the previous archive.
 * A password (for basic security) and release date should be given as parameters.
 *
 * Usage:
 * php path_to/monthly.php -w "password" -d "YYYY-MM-DD"
 * "/Applications/MAMP/bin/php/php5.4.4/bin/php" "/Users/ruud/ETI/Zend workbenches/Current/AC DCA export/monthly.php" -w "^Guu&*^f___\\" -d "2013-12-09"
 *
 *
* Viktor's original instructions
*
1) edit [credits] portion of the settings.ini in DCA_Export_v1.3/config/ change dates in 'string' and 'release_date' variables to 17th October 2013 and 2013-10-17;
2) delete all archives in /var/www/DCA_Export_v1.3/zip/ folder;
3) delete old archive-complete.zip in /var/www/DCA_Export_v1.3/zip-fixed/ folder;
4) on the website http://www.catalogueoflife.com/DCA_Export/ type [All] with the square brackets into 'Top level group' field choose 'Complete data' and click 'Download' button to generate archive.
5) it will take a while... Make sure not to close browser window while it is working;
6) once complete new archive_complete.zip will appear in /var/www/DCA_Export_v1.3/zip/ folder;
7) copy/paste it into /var/www/DCA_Export_v1.3/zip-fixed/ folder once with original name and then second time renaming it 2013-10-17-archive-complete.zip;
8) send the notification e-mail to interested parties. I am ususlly posting the following message to i4Life-WP2 mailing list (I will post it later today):

*/

// Just in case... five hours timeout
set_time_limit(18000);

// Password required when calling the service
$password = '^Guu&*^f___\\';
$configPath =  dirname(__FILE__) . '/config/settings.ini';

require_once 'DCAExporter.php';
require_once 'includes/library.php';
alwaysFlush();


// Get command line parameters
$options = getopt("w:");
$w = isset($options['w']) ? $options['w'] : false;


// Fake data submission
$_POST['kingdom'] = '[all]';
$_POST['block'] = 3;


// Basic security
if (!$w || $w !== $password) {
    die("You did not say the magic word, bye bye\n\n");
}


// Initialize DCAExporter
echo "\n\n\nInitialising...\n";
$dcaExporter = new DCAExporter();
$errors = $dcaExporter->getStartUpErrors();
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "\n";
    }
    die();
}
$dcaExporter->useIndicator();
$dcaExporter->setIndicatorBreakLine("\n");
$dcaExporter->setIndicatorMarkersPerLine(50);
$dcaExporter->setIndicatorIterationsPerMarker(500);

$d = $dcaExporter->getReleaseDateFromDatabase();
$_SESSION['monthly']['ini']['credits']['string'] = 'Species 2000 & ITIS Catalogue of Life: ' .
    date('jS F Y', strtotime($d));
$_SESSION['monthly']['ini']['credits']['release_date'] = date('Y-m-d', strtotime($d));

echo "This script creates a complete DarwinCore Archive of the\n";
echo $_SESSION['monthly']['ini']['credits']['string'] . "\n\n";

// Write new credits to ini file
echo "Updating release date in ini file...\n";
if (!is_writable($configPath)) {
	exit($configPath . " is not writable, required to update release date\n\n");
}
$config = file($configPath);
for ($i = 0; $i < count($config); $i++) {
	if (strpos($config[$i], 'string') === 0) {
		$config[$i] =
            'string = "' . $_SESSION['monthly']['ini']['credits']['string'] . '"' . "\n";
	}
	if (strpos($config[$i], 'release_date') === 0) {
		$config[$i] =
            'release_date = "' . $_SESSION['monthly']['ini']['credits']['release_date'] . '"' . "\n";
	}
}
unlink($configPath);
file_put_contents($configPath, implode('', $config));


// Empty zip directory
$baseDir = DCAExporter::basePath();
echo "Emptying zip directory with saved archives...\n";
DCAExporter::removeDir($baseDir . '/zip');
mkdir($baseDir . '/zip');


// Delete previous complete archive
echo "Deleting zip-fixed/archive-complete.zip...\n";
if (file_exists($baseDir . '/zip-fixed/archive-complete.zip')) {
    unlink($baseDir . '/zip-fixed/archive-complete.zip');
}


// Create the archive.. sit back and relax...
$total = $dcaExporter->getTotalNumberOfTaxa();
if ($total > 0) {
    echo "Creating export for $total ". ($total == 1 ? 'taxon' : 'taxa') . ":\n";
}
echo "Creating meta.xml...\n";
$dcaExporter->createMetaXml();
echo "Writing data to text files...\n";
$dcaExporter->writeData();
$dcaExporter->copyScripts();
echo "\nCompressing to zip archive...\n";
$dcaExporter->zipArchive();
echo "Archive succesfully created!\nRenaming archive...\n";
rename($baseDir . '/zip/archive-complete.zip', $baseDir . '/zip/' .
    $_SESSION['monthly']['ini']['credits']['release_date'] . '-archive-complete.zip');
echo "Ready!\n\n";


?>