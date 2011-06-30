<?php
alwaysFlush();
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
require_once 'DCAExporter.php';
echo '<p style="font-size: 11px; margin: 0 0 25px 0;">Version ' . DCAExporter::getVersion() . "</p>\n";

if (formIsSubmitted()) {
    // $_GET input is validated in application!
    $dcaExporter = new DCAExporter(array(
        $_GET['rank'] => $_GET['taxon']
    ), $_GET['block']);
    // Check if archive already exits; if it does skip export
    if (!$dcaExporter->archiveExists()) {
        $dcaExporter->useIndicator();
        $errors = $dcaExporter->getStartUpErrors();
        if (!empty($errors)) {
            printErrors($errors);
            exit();
        }
        // No errors, ready to go!
        $total = $dcaExporter->getTotalNumberOfTaxa();
        if ($total > 0) {
            echo "<p>Creating export for $total taxa in " . $_GET['rank'] . ' ' . ucfirst(
                $_GET['taxon']) . ".</p>\n";
        }
        else {
            echo '<p>No results found for ' . $_GET['rank'] . ' ' . ucfirst($_GET['taxon']) . 
                '. <a href="index.php">Back to the index</a></p>';
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
    $url = setDownloadUrl();
    $size = getDownloadSize($url);
    echo "<p>Ready! <a href='$url'>Download the zip archive</a> ($size).</p>
        <p><a href='index.php'>Back to the index</a></p>";
}
else {
    $intro = file_get_contents('templates/intro.tpl');
    $select = setSelect();
    $downloadUrl = '/zip-fixed/archive-complete.zip';
    $downloadComplete = '';
    if (file_exists(dirname(__FILE__) . '/' . $downloadUrl)) {
        $downloadComplete = '<p>Download a Darwin Core Archive for the 
            <a href="' . $downloadUrl . '">complete Catalogue of Life</a> 
            (' . getDownloadSize ('/' . $downloadUrl) . ").</p>\n";
    }
    echo Template::decorateString($intro, array(
        'action' => $_SERVER['PHP_SELF'],
        'select' => $select,
        'downloadComplete' => $downloadComplete
    ));
}

function alwaysFlush ()
{
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) {
        ob_end_flush();
    }
    ob_implicit_flush(1);
    set_time_limit(0);
}

function formIsSubmitted ()
{
    if (isset($_GET['rank']) && !empty($_GET['rank']) && isset($_GET['taxon']) && !empty($_GET['taxon'])) {
        return true;
    }
    return false;
}

function printErrors ($errors)
{
    echo '<p><span style="color: red; font-weight: bold;">Error!</span><br>';
    foreach ($errors as $error) {
        echo $error . '<br>';
    }
    echo "</p>\n<p><a href='index.php'>Back to the index</a></p>";
}

function setDownloadUrl ()
{
    $ini = DCAExporter::getExportSettings();
    $url = DCAExporter::$zip . '-' . $_GET['rank'] . '-' . $_GET['taxon'] . '-bl' . $_GET['block'] . '.zip';
    if ($_GET['taxon'] == '[all]') {
        $url = DCAExporter::$zip . '-complete.zip';
    }
    return $url;
}

function getDownloadSize ($url)
{
    $sizeKb = filesize(dirname(__FILE__) . '/' . $url) / 1024;
    $size = round($sizeKb, 1) . ' KB';
    if ($sizeKb > 999) {
        $size = round($sizeKb / 1024, 1) . ' MB';
    }
    return $size;
}

function setSelect ()
{
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
    return $select;
}
?>
</body>
</html>