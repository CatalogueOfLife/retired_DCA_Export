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
require_once 'DCAExporter.php';
/* Simple search criteria in array
   E.g. genus starts with larus becomes array('genus' => 'larus%');
 */
$sc = array('genus' =>'larus%');

$dwaExporter = new DCAExporter($sc);
echo 'Creating meta.xml...<br>'; flush();
$dwaExporter->createMetaXml();
echo 'Creating writing taxa...<br>'; flush();
$dwaExporter->writeTaxa();
echo 'Compressing to zip archive..<br>'; flush();
$dwaExporter->zipArchive();
echo 'Ready!';
?>
</body>
</html>