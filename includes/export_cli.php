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

    $dcaExporter = new DCAExporter();
    $errors = $dcaExporter->getStartUpErrors();
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        die();
    }
    $dcaExporter->useIndicator();
    $dcaExporter->setIndicatorBreakLine("<br>");
    $dcaExporter->setIndicatorMarkersPerLine(50);
    $dcaExporter->setIndicatorIterationsPerMarker(500);
    $dcaExporter->writeData('cli');
    //$dcaExporter->copyScripts();
    //$dcaExporter->zipArchive();
    
?>