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
    if (formSubmitted()) {
        include 'includes/export.php';
    }
    else {
        $intro = file_get_contents('templates/intro.tpl');
        $downloadUrl = '../zip-fixed/archive-complete.zip';
        $downloadComplete = '';
        if (file_exists(DCAExporter::basePath() . '/' . $downloadUrl)) {
            $downloadComplete = '<p>Download a Darwin Core Archive for the 
                <a href="' . $downloadUrl . '">complete Catalogue of Life</a> 
                (' . getDownloadSize('/' . $downloadUrl) . ").</p>\n";
        }
        echo Template::decorateString($intro, 
            array(
                'action' => $_SERVER['PHP_SELF'], 
                'downloadComplete' => $downloadComplete
            ));
        include 'includes/form.php';
    }
?>
</body>
</html>