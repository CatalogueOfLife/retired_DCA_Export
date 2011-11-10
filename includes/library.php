<?php
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

function formSubmitted ()
{
    foreach (Taxon::$higherTaxa as $rank) {
        if (isset($_POST[$rank]) && !empty($_POST[$rank])) {
            return true;
        }
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
?>