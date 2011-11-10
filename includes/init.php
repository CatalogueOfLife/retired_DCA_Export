<?php
session_start();
require_once 'includes/library.php';
require_once 'DCAExporter.php';
alwaysFlush();
foreach (Taxon::$higherTaxa as $rank) {
    if (isset($_POST[$rank]) && !empty($_POST[$rank])) {
        $$rank = $_POST[$rank];
    } else if (isset($_SESSION[$rank]) && !empty($_SESSION[$rank])) {
        $$rank = $_SESSION[$rank];
    } else {
        $$rank = '';
    }
    echo $rank .' = '.$$rank.'<br>';
}
?>