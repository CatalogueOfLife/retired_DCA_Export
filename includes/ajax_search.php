<?php
	require_once '../DbHandler.php';
	// Sanitize input
	$fields = array('rank', 'kingdom', 'phylum', 'order', 'class', 'superfamily', 'family', 'genus');
	foreach ($_GET as $k => $v) {
		if (in_array($k, $fields) && is_string($v)) {
			$$k = htmlentities(trim($v));
		}
	}
	// Build query; decide which table to query
	empty($genus) && $rank != 'genus' ? $table = '_search_family' : $table = '_search_scientific';
	$query = 'SELECT `'.$rank.'` FROM '.$table.' WHERE ';
	$parameters = array();
	foreach ($fields as $field) {
		if ($field != 'rank' && !empty($$field)) {
			$query .= '`'.$field.'` LIKE ? AND ';
			$parameters[] = $$field.'%';
		}
	}
	// Show all is clicked when another taxon has already been selected
	if (empty($$rank)) {
	    $query .= '`'.$rank.'` != "" AND ';
	}
	// Show all is clicked when nothing else has been selected
	if (empty($parameters)) {
		$query .= '`'.$rank.'` != "" AND ';
	}
	$query = substr($query, 0, -4).' GROUP BY `'.$rank.'`';
	$dbh = createDbInstance('db');
	$stmt = $dbh->prepare($query);
	$stmt->execute($parameters);
	$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
	
	if (count($result) < 10000) {
		echo json_encode($result);
	} else {
		echo json_encode(array('Please first select a higher taxon'));
	}
	

    function createDbInstance ($name)
    {
        $ini = parse_ini_file('../config/settings.ini', true);
        $config = $ini['db'];
        $dbOptions = array();
        if (isset($config["options"])) {
            $options = explode(",", $config["options"]);
            foreach ($options as $option) {
                $pts = explode("=", trim($option));
                $dbOptions[$pts[0]] = $pts[1];
            }
            DbHandler::createInstance($name, $config, $dbOptions);
        }
        return DbHandler::getInstance($name);
    }



?>