<?php
require_once 'DCAExporter.php';

// Always flush page
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) {
    ob_end_flush();
}
ob_implicit_flush(1);
set_time_limit(0);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>i4Life WP4 Enhanced Download Service of the Catalogue of Life:
Darwin Core Archive Export</title>
</head>
<body style="font: 12px verdana; width: 800px;">
<img src="images/i4life_logo_sm.jpg" width="150" height="62"
    style="right: 0; float: right; padding: 0 10px;" alt="i4Life">
<h3>i4Life WP4 Enhanced Download Service of the Catalogue of Life:<br>
Darwin Core Archive Export</h3>
<?php
echo '<p style="font-size: 11px; margin-bottom: 30px;">Version '.DCAExporter::getVersion()."</p>\n";
?>

<?php
if (isset($_GET['rank']) && !empty($_GET['rank']) && isset($_GET['taxon']) && !empty($_GET['taxon'])) {
    // $_GET input is validated in application
    $rank = $_GET['rank'];
    $taxon = $_GET['taxon'];
    $block = $_GET['block'];
    $searchCriteria = array(
        $rank => $taxon
    );
    
    $dcaExporter = new DCAExporter($searchCriteria, $block);
    // Check if archive already exits; if it does skip export
    if (!$dcaExporter->archiveExists()) {
        // Archive does not yet exist; create export
        $dcaExporter->useIndicator();
        // Check for errors first
        $errors = $dcaExporter->getStartUpErrors();
        if (!empty($errors)) {
            echo '<p><span style="color: red; font-weight: bold;">Error!</span><br>';
            foreach ($errors as $error) {
                echo $error . '<br>';
            }
            echo "</p>\n<p><a href='index.php'>Back to the index</a></p>";
            exit();
        }
        // No errors, ready to go!
        $total = $dcaExporter->getTotalNumberOfTaxa();
        if ($total > 0) {
            echo "<p>Creating export for $total taxa in $rank " . ucfirst($taxon) . '.</p>';
        }
        else {
            echo "<p>No results found for $rank " . ucfirst($taxon) . '. 
                <a href="index.php">Back to the index</a></p>';
            exit();
        }
        echo '<p>Creating meta.xml...<br>';
        $dcaExporter->createMetaXml();
        echo 'Writing data to text files...<br>';
        $dcaExporter->writeData();
        echo '<br>Compressing to zip archive...<br>';
        $dcaExporter->zipArchive();
        echo "</p>\n";
    }
    
    // Construct download url and calculate file size
    $ini = DCAExporter::getExportSettings();
    $url = $ini['zip_archive'] . "-$rank-$taxon-bl$block.zip";
    $sizeKb = filesize(dirname(__FILE__) . '/' . $url) / 1024;
    $size = round($sizeKb, 1) . ' KB';
    if ($sizeKb > 999) {
        $size = round($sizeKb / 1024, 1) . ' MB';
    }
    echo "<p>Ready! <a href='$url'>Download the zip archive</a> ($size).</p>
        <p><a href='index.php'>Back to the index</a></p>";
}
else {
    $ranks = Taxon::$higherTaxa;
    // Omit rank subgenus as this is not available yet in AC
    $nrRanks = count($ranks) - 1;
    $select = $selected = '';
    for ($i = 0; $i < $nrRanks; $i++) {
        if ($i == ($nrRanks - 1)) {
            // Automatically select genus from popup
            $selected = 'selected';
        }
        $select .= "<option value='$ranks[$i]' $selected>" . ucfirst($ranks[$i]) . "</option>\n";
    }
    
    $intro = file_get_contents('templates/intro.tpl');
    echo str_replace(array(
        '[action]', 
        '[select]'
    ), array(
        $_SERVER['PHP_SELF'], 
        $select
    ), $intro);
}
?>
</body>
</html>