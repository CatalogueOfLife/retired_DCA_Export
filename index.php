<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Catalogue of Life: Darwin Core Archive export</title>
</head>
<body style="font: 12px verdana; width: 600px;">
<h3>Catalogue of Life: Darwin Core Archive export</h3>

<?php
require_once 'Abstract.php';
require_once 'Taxon.php';
$ranks = Taxon::$higherTaxa;

if (isset($_GET['rank']) && !empty($_GET['rank']) && isset($_GET['taxon']) && !empty($_GET['taxon'])) {
    require_once 'DCAExporter.php';
    $rank = $_GET['rank'];
    $taxon = $_GET['taxon'];
    // $_GET input for rank and taxon is validated in application
    $sc = array(
        $rank => $taxon
    );
    
    $dwaExporter = new DCAExporter($sc);
    echo '<p>Creating meta.xml...<br>';
    flush();
    $dwaExporter->createMetaXml();
    echo 'Writing data...<br>';
    flush();
    $dwaExporter->writeData();
    echo 'Compressing to zip archive..<br>';
    flush();
    $dwaExporter->zipArchive();
    
    $ini = $dwaExporter->getExportSettings();
    $pathInfo = pathinfo($_SERVER['PHP_SELF']);
    $downloadUrl = $_SERVER['HTTP_HOST'] . $pathInfo['dirname'] . '/' . $ini['zip_archive'] . "-$rank-$taxon.zip";
    echo "</p>\n<p>Ready! <a href='$downloadUrl'>Download the zip archive</a>.</p>";
}
else {
    echo '<p>This page offers a very basic interface on the application that exports data from the Catalogue of Life
          in the <a href="http://code.google.com/p/gbif-ecat/wiki/DwCArchive">Darwin Core Archive format</a>.</p><p>
          Select a rank from the popup menu and enter a taxon name to start the process. The name should match
          exactly, wildcards are not allowed. Note that the higher the rank, the longer the export process will
          take, so for demonstration purpose it is best to select a family or genus.</p>';
    echo "\n<form id='postQuery' action='" . $_SERVER['PHP_SELF'] . "' method='get'>\n<select name='rank'>\n";
    $nrRanks = count($ranks) - 1;
    $selected = '';
    for ($i = 0; $i < $nrRanks; $i++) {
        if ($i == ($nrRanks - 1)) {
            // Automatically select genus from popup
            $selected = 'selected';
        }
        echo "<option value='$ranks[$i]' $selected>" . ucfirst($ranks[$i]) . "</option>\n";
    }
    echo '</select>
          <input type="text" name="taxon" />
          <input type="submit" name="submit" value="Start" />
          </form>';
}
?>
</body>
</html>