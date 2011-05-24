<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Catalogue of Life: Darwin Core Archive export</title>
</head>
<body style="font: 12px verdana;">
<h3>Catalogue of Life: Darwin Core Archive export</h3>
<p>This script provides an export of the Catalogue of Life in the Darwin
Core Archive format.</p>

<?php
require_once 'DbHandler.php';
require_once 'DCAExporter.php';

$ini = parse_ini_file('config/settings.ini', true);
$config = $ini['db'];
$dbOptions = array();
if (isset($config["options"])) {
    $options = explode(",", $config["options"]);
    foreach ($options as $option) {
        $pts = explode("=", trim($option));
        $dbOptions[$pts[0]] = $pts[1];
    }
    DbHandler::createInstance('db', $config, $dbOptions);
}

$dbh = DbHandler::getInstance('db');
$dir = $ini['export']['export_dir'];
$del = $ini['export']['delimiter'];
$sep = $ini['export']['separator'];

/* Simple search criteria in array, .e.g. 'genus = larus' becomes
 * array ('genus' => 'larus');
 */

$searchCriteria();

$dwaExporter = new DCAExporter($dbh, $dir, $del, $sep);
$dwaExporter->setSearchCriteria($searchCriteria);
$dwaExporter->writeTaxa();

?>
</body>
</html>