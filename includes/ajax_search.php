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
	$table = empty($genus) && $rank != 'genus' ? '_search_family' : '_search_scientific';
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
	// If _search_scientific is used, make sure only accepted taxa are returned!
    $query = substr($query, 0, -4);
    if ($table == '_search_scientific') {
        $query .= ' AND `status` = 0 ';
    }
	$query .= ' GROUP BY `'.$rank.'` ORDER BY `'.$rank.'`';
	$dbh = createDbInstance('db');
	$stmt = $dbh->prepare($query);
	$stmt->execute($parameters);
	$total = $stmt->rowCount();

	if ($total < 10000) {
	    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
	} else {
	    $result = array('too_many');
	}
	echo json_encode($result);


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