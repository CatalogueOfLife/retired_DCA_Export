<?php
// POST data is filtered/verified in class!
$dcaExporter = new DCAExporter();
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
    // If the total exceeds 100.000 records, offload to cli
    if ($total > 10) {
        passthru($dcaExporter->getExportCliCommand());
        passthru($dcaExporter->getZipCliCommand());
    // Webserver should be able to handle smaller exports
    } else {
        $dcaExporter->writeData();
        $dcaExporter->copyScripts();
        $dcaExporter->zipArchive();
    }
    echo "</p>\n<p>Ready! ";
// That was easy
}  else {
    echo '<p>This export was already created earlier! ';
}
$url = DCAExporter::$zip . $dcaExporter->getZipArchiveName();
$size = DCAExporter::getDownloadSize($dcaExporter->getZipArchivePath());
// Need to destroy the object to allow it to do some house cleaning
//unset($dcaExporter);
echo "<a href='$url'>Download the zip archive</a> ($size).</p>
        <p><a href='index.php'>Back to the index</a></p>";
?>