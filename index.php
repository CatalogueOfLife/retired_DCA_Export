<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Catalogue of Life: Darwin Core Archive export</title>
</head>
<body style="font: 12px verdana; width: 600px;">
<h3>Catalogue of Life: Darwin Core Archive export</h3>

<?php
if (isset($_GET['rank']) && !empty($_GET['rank']) && isset($_GET['taxon']) && !empty($_GET['taxon'])) {
    
    // $_GET input is validated in application
    $rank = $_GET['rank'];
    $taxon = $_GET['taxon'];
    $searchCriteria = array(
        $rank => $taxon
    );
    // Construct download url
    require_once 'DCAExporter.php';
    $dcaExporter = new DCAExporter($searchCriteria);
    $ini = $dcaExporter->getExportSettings();
    $info = pathinfo($_SERVER['PHP_SELF']);
    $url = $_SERVER['HTTP_HOST'] . $info['dirname'] . '/' . $ini['zip_archive'] . "-$rank-$taxon.zip";
    
    echo '<p>Creating meta.xml...<br>';
    flush();
    $dcaExporter->createMetaXml();
    echo 'Writing data...<br>';
    flush();
    $dcaExporter->writeData();
    echo 'Compressing to zip archive..<br>';
    flush();
    $dcaExporter->zipArchive();
    echo "</p>\n<p>Ready! <a href='$url'>Download the zip archive</a>.</p>";
}
else {
    require_once 'Abstract.php';
    require_once 'Taxon.php';
    $ranks = Taxon::$higherTaxa;
    // Omit rank subgenus as this is not available yet in AC
    $nrRanks = count($ranks) - 1;
    $selected = '';
    
    echo '<p>This page offers a very basic interface on the application that exports data from the Catalogue of Life
          in the <a href="http://code.google.com/p/gbif-ecat/wiki/DwCArchive">Darwin Core Archive format</a>.</p><p>
          Select a rank from the popup menu and enter a taxon name to start the export. The name should match
          exactly, wildcards are not allowed. Note that the higher the rank, the longer the export process will
          take, so for demonstration purpose it is best to select a family or genus.</p>';
    echo "\n<form style='margin-top: 30px;' action='" . $_SERVER['PHP_SELF'] . "' method='get'>\n<select name='rank'>\n";
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