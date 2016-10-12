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

function createDbInstance ($name)
{
    $ini = parse_ini_file('config/settings.ini', true);
    $config = $ini['db'];
    $dbOptions = array();
    if (isset($config["options"])) {
        $options = explode(",", $config["options"]);
        foreach ($options as $option) {
            $pts = explode("=", trim($option));
            $dbOptions[$pts[0]] = $pts[1];
        }
        return DbHandler::createInstance($name, $config, $dbOptions);
    }
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

function downloadComplete ()
{
    $downloadUrl = 'zip-fixed/archive-complete.zip';
    if (file_exists(DCAExporter::basePath() . '/' . $downloadUrl)) {
        $output = '<p>Download a Darwin Core Archive for the
            <a href="' . $downloadUrl . '">complete Catalogue of Life</a>
            (' . DCAExporter::getDownloadSize($downloadUrl) . ')';
    }
    if (count(DCAExporter::getPreviousEditions()) > 0) {
    	if (!isset($output)) {
    		$output = '<p><a href="archive.php">Download a previous edition</a>.';
    	} else {
    		$output .= ' or <a href="archive.php">download a previous edition</a>.';
    	}
    }
    return isset($output) ? $output : false;
}

function printEditions ()
{
    $editions = DCAExporter::getPreviousEditions();
    $output = '';
    if (count($editions['monthly']) > 0) {
        $output .= "<p>Monthly editions:</p>\n<ul>\n";
        foreach ($editions['monthly'] as $ed) {
            $output .= '<li><a href="' . $ed['url'] . '">' . $ed['name'] .
                '</a> ('.$ed['size'].')</li>' . "\n";
        }
        $output .= "</ul>\n";
    }
    if (count($editions['annual']) > 0) {
        $output .= "<p>Annual editions:</p>\n<ul>\n";
        foreach ($editions['annual'] as $ed) {
            $output .= '<li><a href="' . $ed['url'] . '">' . $ed['name'] .
                '</a> ('.$ed['size'].')</li>' . "\n";
        }
        $output .= "</ul>\n";
    }
    return $output;
}

function setDynamicVersion ()
{
    $d = key(DCAExporter::getPreviousEditions());
    if (!empty($d)) {
        $f = substr($d, 0, 4) . '-' . substr($d, 4, 2) . '-' . substr($d, 6, 2);
        $_SESSION['monthly']['ini']['credits']['string'] =
            'Species 2000 & ITIS Catalogue of Life: ' . date('jS F Y', strtotime($f));;
        $_SESSION['monthly']['ini']['credits']['release_date'] = $f;
    }
}


?>