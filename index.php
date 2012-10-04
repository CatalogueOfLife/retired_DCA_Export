<?php
    // Sets form variables, session start and several includes
    require_once 'includes/init.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php
    // Sets jQuery and stylesheet paths
    require_once 'includes/head.php';
?>
</head>
<body>
<img src="images/i4life_logo_sm.jpg" width="150" height="62"
    style="right: 0; float: right; padding: 0 10px;" alt="i4Life">
<h3>i4Life WP4 Download Service of the Catalogue of Life:<br>
Darwin Core Archive Export</h3>
<?php
    echo '<p class="version">Version ' . DCAExporter::getVersion() . "</p>\n";
    // Test database handler first
    createDbInstance('db');
    if (!(DbHandler::getInstance('db') instanceof PDO)) {
        exit ('<br>Could not create database instance; 
            check settings in settings.ini!</body></html>');
    }
    if (formSubmitted()) {
        include 'includes/export.php';
    }
    else {
        $intro = file_get_contents('templates/intro.tpl');
        echo Template::decorateString($intro, 
            array(
                'colEdition' => htmlspecialchars(DCAExporter::getEdition()),
                'webserviceUrl' => DCAExporter::getWebserviceUrl(),
                'zipScripts' => DCAExporter::zipScripts(),
                'downloadComplete' => downloadComplete()
            ));
        include 'includes/form.php';
    }
?>
</body>
</html>