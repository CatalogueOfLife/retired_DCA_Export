<?php
// $_GET input is validated in application!
$dcaExporter = new DCAExporter(array(
    $_GET['rank'] => $_GET['taxon']
), $_GET['block']);
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
        echo "<p>Creating export for $total taxa in " . $_GET['rank'] . ' ' . ucfirst($_GET['taxon']) . ".</p>\n";
    }
    else {
        echo '<p>No results found for ' . $_GET['rank'] . ' ' . ucfirst($_GET['taxon']) . '. <a href="index.php">Back to the index</a></p>';
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
?>