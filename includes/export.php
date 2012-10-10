<?php
// POST data is filtered/verified in class!
$dcaExporter = new DCAExporter(DCAExporter::filterSc($_POST), $_POST['block']);
// Check if archive already exists; if it does skip export
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
        echo "<p>Creating export for $total ". ($total == 1 ? 'taxon' : 'taxa') . "</p>\n";
    }
    else {
        echo '<p>No results found, please <a href="index.php">adjust your search criteria</a></p>';
        exit();
    }
    echo '<p>Creating meta.xml...<br>';
    $dcaExporter->createMetaXml();
    echo 'Writing data to text files...<br>';
    $dcaExporter->writeData();
    $dcaExporter->copyScripts();
    echo '<br>Compressing to zip archive...<br>';
    $dcaExporter->zipArchive();
    echo "</p>\n";
}
$url = DCAExporter::$zip . DCAExporter::getZipArchiveName();
$size = DCAExporter::getDownloadSize(DCAExporter::getZipArchivePath());
// Need to destroy the object to allow it to do some house cleaning
//unset($dcaExporter);
echo "<p>Ready! <a href='$url'>Download the zip archive</a> ($size).</p>
        <p><a href='index.php'>Back to the index</a></p>";
?>