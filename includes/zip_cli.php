<?php

    require_once 'DCAExporter.php';
    require_once 'library.php';
    
    alwaysFlush();
    
    // Emulate a POST by transforming the cli arguments
    foreach ($argv as $arg) {
        $e = explode("=", $arg);
        if (count($e) == 2) {
            $_POST[substr($e[0], 2)] = $e[1];
        }
    }
    // Do not bootstrap, we want to keep the temp dir!
    $dcaExporter = new DCAExporter(false);
    $dcaExporter->copyScripts();
    $dcaExporter->zipArchive();
    
?>