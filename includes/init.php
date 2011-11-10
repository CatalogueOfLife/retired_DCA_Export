<?php
session_start();
require_once 'includes/library.php';
require_once 'DCAExporter.php';
alwaysFlush();
$vars = setVars();
foreach ($vars as $var) {
    if (isset($_POST[$var]) && !empty($_POST[$var])) {
        $$var = $_POST[$var];
    } else if (isset($_SESSION[$var]) && !empty($_SESSION[$var])) {
        $$var = $_SESSION[$var];
    } else {
        $$var = '';
    }
    echo $var .' = '.$$var.'<br>';
}
?>