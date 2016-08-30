<?php
require_once 'DbHandler.php';
require_once 'Bootstrap.php';
require_once 'Indicator.php';
require_once 'Zip.php';
require_once 'Template.php';
require_once 'modules/Abstract.php';
require_once 'modules/Taxon.php';
require_once 'modules/Vernacular.php';
require_once 'modules/Distribution.php';
require_once 'modules/Description.php';
require_once 'modules/Reference.php';
require_once 'modules/SourceDatabase.php';
require_once 'modules/SpeciesProfile.php';

class DCAExporter
{
    // Export directory
    public static $dir = 'export/';
    // Name of meta.xml template
    public static $meta = 'templates/meta.tpl';
    // Base name of zip archive
    public static $zip = 'zip/';

    // Database handler
    private $_dbh;
    // Text file delimiter
    private $_del;
    // Text file separator
    private $_sep;
    // Search criteria
    private $_sc;
    // Block level; determines amount of data returned
    private $_bl;
    // Path to save files to
    private $_dir;
    // Path to meta file template
    private $_meta;
    // Path to zip archive to
    private $_zip;
    // Source databases excluded from results
    private $_excluded;
    // Include fossils
    private $_fossils;
    // Taxon object to be written
    private $_taxon;

    // Storage array to determine if an eml file has already been written
    private $_savedEmls = array();
    // Storage array to determine if specific references has already been written
    private $_savedReferences = array();
    // Indicator to show progress
    private $_indicator;
    // Number of markers per line for indicator
    private $_indicatorMarkersPerLine = 75;
    // Number of iterations per markers for indicator
    private $_indicatorIterationsPerMarker = 50;
    // ignore_user_abort() original setting (will be restored)
    private $_iuaSetting;
    // Should all taxa be exported?
    private $_completeDump;

    // Collects bootstrap errors
    public $startUpErrors;

    public function __construct ($sc, $bl)
    {
        $this->_createDbInstance('db');
        $this->_dbh = DbHandler::getInstance('db');
        $ini = parse_ini_file('config/settings.ini', true);
        $this->_del = $ini['export']['delimiter'];
        $this->_sep = $ini['export']['separator'];
        $this->_fossils = $ini['export']['fossils'];
        $this->_sc = self::filterSc($sc);
        $this->_bl = $bl;
        $this->_dir = self::basePath() . '/' . self::$dir . md5(self::getZipArchiveName()) . '/';
        $this->_meta = self::basePath() . '/' . self::$meta;
        $this->_zip = self::basePath() . '/' . self::$zip;
        $this->_excluded = $this->_setExcluded($ini);
        $this->_setDefaults();
        $this->_iuaSetting = ignore_user_abort(true);
        set_time_limit(0);
        $this->_completeDump = in_array('[all]', $this->_sc) ? true : false;

        $bootstrap = new DCABootstrap($this->_dbh, $this->_del, $this->_sep, $this->_sc,
            $this->_bl, $this->_dir, $this->_zip, $this->_excluded);
        $this->startUpErrors = $bootstrap->getErrors();
        unset($bootstrap);
    }

    public function __destruct ()
    {
        $this->deleteTempDir();
        // Reset ignore_user_abort back to 0 if this was set as such in php.ini
        if ($this->_iuaSetting == 0) {
            ignore_user_abort(false);
        }
    }

    public static function getExportSettings ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['export'];
    }

    public static function getVersion ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['settings']['version'] . ' [r' . $ini['settings']['revision'] . ']';
    }

    public static function getEdition ()
    {
        if (isset($_SESSION['monthly'])) {
            return $_SESSION['monthly']['ini']['credits']['string'] .
                ' (' . $_SESSION['monthly']['ini']['credits']['release_date'] . ')';
        }
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['credits']['string'] . ' (' . $ini['credits']['release_date'] . ')';
    }

    public static function getCredits ()
    {
        if (isset($_SESSION['monthly'])) {
            return $_SESSION['monthly']['ini']['credits']['string'];
        }
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['credits']['string'];
    }

    public static function getWebserviceUrl ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['webservice']['url'];
    }

    public static function filterSc ($sc)
    {
        $filteredSc = array();
        foreach ($sc as $rank => $taxon) {
            if (in_array(strtolower($rank), Taxon::$higherTaxa) && $taxon != '') {
                $filteredSc[strtolower($rank)] = strtolower($taxon);
            }
        }
        return $filteredSc;
    }

    public static function removeDir ($dir)
    {
        // Exit if directory to be deleted is not a subdirectory of the DCA installation
        if (strstr($dir, '..') !== false || strpos($dir, self::basePath()) !== 0) {
            return false;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        self::removeDir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

	public static function copyDir ($source, $destination) {
		if (file_exists($destination)) {
			self::removeDir($destination);
		}
		if (is_dir($source)) {
			// Skip directories starting with . (svn!)
			if (substr(basename($source), 0, 1) != ".") {
				mkdir($destination);
				$files = scandir($source);
				foreach ($files as $file) {
					if ($file != "." && $file != "..") {
						self::copyDir("$source/$file", "$destination/$file");
					}
				}
			}
		}
		else if (file_exists($source)) {
			copy($source, $destination);
		}
	}

    public function getZipArchiveName ()
    {
        $url = 'archive-';
        if (in_array('[all]', $this->_sc)) {
            return $url . 'complete.zip';
        }
        foreach ($this->_sc as $rank => $taxon) {
            $url .= strtolower($rank) . '-' . strtolower($taxon) . '-';
        }
        $url .= 'bl' . $this->_bl . '.zip';
        return $url;
    }

    public function getZipArchivePath ()
    {
        return self::basePath() . '/'. self::$zip . $this->getZipArchiveName();
    }

    public static function basePath ()
    {
        return dirname(__FILE__);
    }

    public function getReleaseDateFromDatabase ()
    {
        $query = 'SELECT `edition` FROM `_credits` WHERE `current` = 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['edition'] : false;
    }

    public static function getPreviousEditions ()
    {
        // Previous editions should be named yyyy-mm-dd-archive-complete.zip
        // and stored in the 'zip-fixed' directory
        $files = array();
        $dir = self::basePath() . '/zip-fixed';
        $path = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? $http = 'https://' : $http = 'http://';
        $d = dir($dir);
        while (false !== ($file = $d->read())) {
            if (is_numeric(substr($file, 0, 4)) && !is_dir($file)) {
                list($year, $month, $day) = explode('-', $file);
                $files[$year.$month.$day] = array(
                    'edition' => date("j F Y", mktime(0, 0, 0, $month, $day, $year)),
                    'size' => self::getDownloadSize($dir .'/' . $file),
                    'url' => /*$http . substr($path, 0, -(strlen(basename($path)))).*/ 'zip-fixed/' . $file
                );
            }
        }
        $d->close();
        krsort($files);
        return $files;
    }

    public static function getDownloadSize ($path)
    {
        $sizeKb = filesize($path) / 1024;
        $size = round($sizeKb, 1) . ' KB';
        if ($sizeKb > 999) {
            $size = round($sizeKb / 1024, 1) . ' MB';
        }
        return $size;
    }

    public static function zipScripts ()
    {
        $zip = new Zip();
        $dest = self::basePath() . '/' .self::$zip . 'import-scripts.zip';
        $zip->createArchive(self::basePath() . '/templates/import-scripts', $dest);
        unset($zip);
        return $dest;
    }

    private function _createDbInstance ($name)
    {
        $ini = parse_ini_file('config/settings.ini', true);
        $config = $ini[$name];
        $dbOptions = array();
        if (isset($config["options"])) {
            $options = explode(",", $config["options"]);
            foreach ($options as $option) {
                $pts = explode("=", trim($option));
                $dbOptions[$pts[0]] = $pts[1];
            }
            DbHandler::createInstance($name, $config, $dbOptions);
        }
    }

    private function _buildQuery ($t = null)
    {
        // Possible query types $t are null (taxa), 'sn' (synonyms) and 'tt' (count total)
        $query = 'SELECT ';
        // Count only if total has to be determined
        if ($t == 'tt') {
            $query .= 'COUNT(`id`) ';
        // Complete query for taxa and synonyms
        } else {
            $query .= '`id` AS taxonID,
                IF (`source_database_id` > 0,
                    `source_database_id`,
                    "Species 2000"
                ) AS datasetID,
                IF (`source_database_name` != "",
                    `source_database_name`,
                    "Catalogue of Life"
                ) AS datasetName,
                `kingdom`,
                `phylum`,
                `class`,
                `order`,
                `superfamily`,
                `family`,
                `genus`,
                `genus` AS genericName,
            	IF (`accepted_species_id` > 0,
                    `accepted_species_id`,
                    ""
                ) AS acceptedNameUsageID,
                IF (`accepted_species_id` > 0,
                    "",
                    IF (`is_extinct` = 0,
                        "false",
                        "true"
                    )
                ) AS isExtinct, ';
            // Split query based on block level; level 1 only exports classification up to genus
            if ($this->_bl == 1 && !$this->_completeDump && $t != 'sn') {
                $query .=
                    '"" AS status,
                    `subgenus`,
                    "" AS specificEpithet,
                    "" AS infraspecificEpithet,
                    "" AS verbatimTaxonRank,
                    "" AS scientificNameAuthorship ';
            } else {
                $query .=
                    '`status`,
                    `subgenus`,
                    `species` AS specificEpithet,
                    `infraspecies` AS infraspecificEpithet,
                    `infraspecific_marker` AS verbatimTaxonRank,
                    `author` AS scientificNameAuthorship ';
            }
        }
        $query .= ' FROM `_search_scientific` WHERE ';
        // Synonyms
        if ($t == 'sn') {
            $query .= '`accepted_species_id` = ?';
            return $this->_excludedToQuery($query);
        // Taxa
        } else if (!$this->_completeDump) {
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` = :$field AND ";
            }
            // Omit (infra)species from level 1
            if ($this->_bl == 1) {
                $query .= '`species` = "" AND `infraspecies` = "" AND ';
            }
        }
        if ($this->_fossils == 0) {
            $query .= '`is_extinct` = 0 AND ';
        }
   		$query = $this->_excludedToQuery($query . '`accepted_species_id` = 0 ');

        // Omit limit from total query
        if ($t != 'tt') {
            $query .= 'LIMIT :limit OFFSET :offset';
        }
        return $query;
    }

    private function _addSavedEml ($srcDbId)
    {
        $this->_savedEmls[] = $srcDbId;
    }

    private function _emlExists ($srcDbId)
    {
        return in_array($srcDbId, $this->_savedEmls) ? true : false;
    }

    private function _setDefaults ()
    {
        if (empty($this->_del)) {
            $this->_del = ',';
        }
        if (empty($this->_sep)) {
            $this->_sep = '"';
        }
        // Tab delimited is a special case
        // fputcsv only accepts single character del and sep
        if ($this->_del == '\t') {
            $this->_del = "\t";
            $this->_sep = "";
        }
    }

    private function _writeDistributions ($distributions, $module) {
        if (!empty($distributions)) {
            foreach ($distributions as $iDs => $rowDs) {
                $distribution = $this->_initModule(
                    $module,
                    $rowDs,
                    $this->_taxon->taxonID);
                $distribution->writeModel();
                unset($distribution);
            }
        }
    }

    private function _initModule ($module, array $data, $taxonId = null)
    {
        try {
            $module = new $module($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            if ($taxonId) {
                $module->taxonID = $taxonId;
            }
            $module->decorate($data);
        }
        catch (Exception $e) {
            return $e;
        }
        return $module;
    }

    private function _getTaxa ($limit, $offset)
    {
        $query = $this->_buildQuery();
        $stmt = $this->_dbh->prepare($query);
        if (!$this->_completeDump) {
            foreach ($this->_sc as $field => $value) {
                $stmt->bindValue(':' . $field, $value);
            }
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getSynonyms ($taxon_id)
    {
        $query = $this->_buildQuery('sn');
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $taxon_id
        ));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getVernaculars ($taxon_id)
    {
        /*
        $query = 'SELECT t3.`name` as vernacularName,
                          t2.`name` as language,
                          t1.`country_iso` as countryCode,
                          t6.`name` AS locality,
                          t5.`authors`,
                          t5.`year`,
                          t5.`title`,
                          t5.`text`,
                          t1.`id` as vernacularID
                   FROM `common_name` t1
                   LEFT JOIN `language` AS t2 ON t2.`iso` = t1.`language_iso`
                   LEFT JOIN `common_name_element` AS t3 ON t3.`id` = t1.`common_name_element_id`
                   RIGHT JOIN `reference_to_common_name` AS t4 ON t4.`common_name_id` = t1.`id`
                   RIGHT JOIN `reference` AS t5 ON t5.`id` = t4.`reference_id`
                   LEFT JOIN `country` AS t6 ON t1.`country_iso` = t6.`iso`
                   WHERE t1.`taxon_id` = ?';
        */
        $query = 'SELECT t3.`name` as vernacularName,
                          t2.`name` as language,
                          t1.`country_iso` as countryCode,
                          t6.`name` AS locality,
                          t1.`id` as vernacularID
                   FROM `common_name` t1
                   LEFT JOIN `language` AS t2 ON t2.`iso` = t1.`language_iso`
                   LEFT JOIN `common_name_element` AS t3 ON t3.`id` = t1.`common_name_element_id`
                   LEFT JOIN `country` AS t6 ON t1.`country_iso` = t6.`iso`
                   WHERE t1.`taxon_id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $taxon_id
        ));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getReferences ($id, $type)
    {
        if (!in_array($type, array(
            'taxon',
            'synonym',
            'common_name'
        ))) {
            throw new Exception('Incorrect reference type: ' . $type);
        }
        $type == 'common_name' ? $printType = 'vernacular' : $printType = $type;
        $query = 'SELECT t1.`title`,
                         t1.`authors` AS creator,
                         t1.`year` AS date,
                         t1.`text` AS description,
                         t2.`resource_identifier` AS identifier,
                         "' . $printType . '" AS type
                  FROM `reference` t1
                  LEFT JOIN `uri` AS t2 ON t1.`uri_id` = t2.`id`
                  LEFT JOIN `reference_to_' . $type . '` AS t3 ON t1.`id` = t3.`reference_id`
                  WHERE t3.`' . $type . '_id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $id
        ));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getLifezones ($taxon_id)
    {
    	$query = 'SELECT t1.`lifezone` AS habitat FROM `lifezone` t1
				  LEFT JOIN `lifezone_to_taxon_detail` AS t2 ON t2.`lifezone_id` = t1.`id`
				  WHERE t2.`taxon_detail_id` = ?';
    	$stmt = $this->_dbh->prepare($query);
    	$stmt->execute(array(
    			$taxon_id
    	));
    	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	return $res ? $res : array();
    }

    private function _getDistributions ($taxon_id, $distributionFreeText = false)
    {
        if (!$distributionFreeText) {
            $query = 'SELECT
                         t2.`status` AS establishmentMeans,
            			 CONCAT(
                             IF(t3.`region_standard_id` = 1 OR t3.`region_standard_id` = 4, "TDWG:",
                			 IF(t3.`region_standard_id` = 2, "IHO:",
                			 IF(t3.`region_standard_id` = 3, "EEZ-VLIZ:", ""
                			 ))),
                             t3.`original_code`
                         ) AS locationID,
                         t3.`name` AS locality,
                         "" AS occurrenceStatus
                      FROM `distribution` AS t1
                      LEFT JOIN `distribution_status` AS t2 ON t1.`distribution_status_id` = t2.`id`
                      LEFT JOIN `region` AS t3 ON t1.`region_id` = t3.`id`
                      WHERE t1.`taxon_detail_id` = ?';
        }
        else {
            $query = 'SELECT t2.`free_text` AS description
                      FROM `distribution_free_text` AS t1
                      LEFT JOIN `region_free_text` AS t2 ON t1.`region_free_text_id` = t2.`id`
                      WHERE t1.`taxon_detail_id` = ?';
        }
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $taxon_id
        ));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getSourceDatabaseMetadata ($source_database_id)
    {
        $query = 'SELECT t1.`id`,
                    CONCAT(t1.`name`, " in the Catalogue of Life") AS title,
                    t1.`abbreviated_name` AS abbreviatedName,
                    t1.`group_name_in_english` AS groupName,
                    t1.`authors_and_editors` AS authorsEditors,
                    t1.`version` AS version,
                    t1.`release_date` AS pubDate,
                    t1.`abstract` AS abstract,
                    t1.`contact_person` AS contact,
                    t3.resource_identifier AS sourceUrl,
                    "" AS contactCity,
                    "" AS contactCountry,
                    CONCAT("images/databases/", REPLACE(`abbreviated_name`, " ", "_"),
                        ".png") AS resourceLogoUrl
                  FROM `source_database` t1
                  LEFT JOIN `uri_to_source_database` AS t2 ON t1.`id` = t2.`source_database_id`
                  LEFT JOIN `uri` AS t3 ON t2.`uri_id` = t3.`id`
                  WHERE t1.`id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $source_database_id
        ));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getTotals ()
    {
        $query = 'SELECT `description`, `total` FROM `_totals`';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($res)) {
            $fields = array();
            foreach ($res as $r) {
                switch ($r['description']) {
                    case 'species':
                        $fields['nrSpecies'] = number_format($r['total']);
                        break;
                    case 'source_databases':
                        $fields['nrDatabases'] = number_format($r['total']);
                        break;
                    case 'infraspecies':
                        $fields['nrInfraspecies'] = number_format($r['total']);
                        break;
                    case 'synonyms':
                        $fields['nrSynonyms'] = number_format($r['total']);
                        break;
                    case 'common_names':
                        $fields['nrCommonNames'] = number_format($r['total']);
                        break;
                    default:
                        break;
                }
            }
            return $fields;
        }
        return false;
    }

    private function _initEml ()
    {
        // Initialize with CoL metadata, so archive always contains this EML file
        $sp2000 = new SourceDatabase($this->_dbh, $this->_dir);
        // Clear dir from previous export first
        $sp2000->resetEmlDir();
        $sp2000->writeEml();
        $this->_addSavedEml("Species 2000");

        // New per 31-05-13: Add second metadata eml for entire CoL
        $sp2000 = new SourceDatabase($this->_dbh, $this->_dir, $this->_getTotals());
        $sp2000->writeEml();
        unset($sp2000);
    }

    private function _setExcluded ($ini)
    {
    	if (isset($ini['excluded_source_dbs']['ids']) && !empty($ini['excluded_source_dbs']['ids'])) {
    		$excluded = explode(',', $ini['excluded_source_dbs']['ids']);
    		return !empty($excluded) ? $excluded : false;
    	}
    	return false;
    }

    private function _excludedToQuery ($query)
    {
    	if ($this->_excluded) {
    		return $query . ' AND `source_database_id` NOT IN (' . implode(',', $this->_excluded) . ') ';
    	}
    	return $query;
    }

    public function getStartUpErrors ()
    {
        return $this->startUpErrors;
    }

    public function deleteTempDir ()
    {
        // Do NOT remove temporary directory if another export process
        // is still running! Error code = 3
        // Full path to temporary directory should be given
        // as this method is called in __destruct
        if (!isset($this->startUpErrors[3])) {
            self::removeDir($this->_dir);
        }
    }

    public function getTotalNumberOfTaxa ()
    {
        $query = $this->_buildQuery('tt');
        $stmt = $this->_dbh->prepare($query);
        foreach ($this->_sc as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_NUM);
        return $res ? $res[0] : false;
    }

    public function useIndicator ()
    {
        $this->_indicator = new Indicator();
    }

    public function setIndicatorBreakLine ($v)
    {
        $this->_indicator->setBreakLine($v);
    }

    public function setIndicatorMarkersPerLine ($v)
    {
        $this->_indicatorMarkersPerLine = $v;
    }

    public function setIndicatorIterationsPerMarker ($v)
    {
        $this->_indicatorIterationsPerMarker = $v;
    }

    public function writeData ()
    {
        $total = $this->getTotalNumberOfTaxa();
        $this->_indicator ? $this->_indicator->init($total, $this->_indicatorMarkersPerLine,
            $this->_indicatorIterationsPerMarker) : null;
        $this->_indicator->setDir($this->_dir);
        $this->_initEml();

        for ($limit = 1000, $offset = 0; $offset < $total; $offset += $limit) {
            $taxa = $this->_getTaxa($limit, $offset);
            foreach ($taxa as $iTx => $rowTx) {
                // In DCA export, relation between reference and type is sometimes lost
                // Check is needed to avoid duplicates; $savedReferences is used for this
                // It stores serialized and subsequently hashed versions of the original arrays
                $this->resetStoredReferences();

                $this->_indicator ? $this->_indicator->iterate() : '';
                $this->_taxon = $this->_initModule('Taxon', $rowTx);
                $this->_taxon->setDefaultTaxonData();
                $this->_taxon->setLsid();
                $this->_taxon->setParentId();

                if (!$this->_emlExists($this->_taxon->datasetID)) {
                    $sourceDatabase = new SourceDatabase(
                        $this->_dbh,
                        $this->_dir,
                        $this->_getSourceDatabaseMetadata(
                            $this->_taxon->datasetID));
                    $sourceDatabase->writeEml();
                    unset($sourceDatabase);
                    $this->_addSavedEml($this->_taxon->datasetID);
                }

                // Remaining data is exported only for (infra)species
                // and for Block levels II to IV
                if (!$this->_taxon->isHigherTaxon && $this->_bl > 1) {
                    $this->_taxon->setScrutiny();
                    $this->_taxon->setScientificNameId();
                    $this->_taxon->setTaxonConceptId();

                    //Synonyms
                    $synonyms = $this->_getSynonyms($this->_taxon->taxonID);
                    foreach ($synonyms as $iSn => $rowSn) {
                        $synonym = $this->_initModule(
                            'Taxon',
                            $rowSn
                        );
                        $synonym->setDefaultTaxonData();
                        $synonym->setScientificNameId();
                        $synonym->setTaxonConceptId();
                        $synonym->setSynonymGenus($this->_taxon);
                        $synonym->setColUrl();
                        $synonym->writeModel();
                        unset($synonym);
                    }

                    // Vernaculars
                    $vernaculars = $this->_getVernaculars($this->_taxon->taxonID);
                    foreach ($vernaculars as $iVn => $rowVn) {
                        $vernacular = $this->_initModule(
                            'Vernacular',
                            $rowVn,
                            $this->_taxon->taxonID);
                        $vernacular->setSource();
                        $vernacular->writeModel();
                        // Vernacular references
                        $references = $this->_getReferences(
                            $vernacular->vernacularID,
                            'common_name');
                        foreach ($references as $iRf => $rowRf) {
                            if ($this->referenceExists($rowRf)) continue;
                            $reference = $this->_initModule(
                                'Reference',
                                $rowRf,
                                $this->_taxon->taxonID);
                            $reference->writeModel();
                            unset($reference);
                        }
                        unset($vernacular);
                    }

                    // Taxon/ synonym references
                    !$this->_taxon->isSynonym ? $type = 'taxon' : $type = 'synonym';
                    $references = $this->_getReferences(
                        $this->_taxon->taxonID,
                        $type);
                    foreach ($references as $iRf => $rowRf) {
                        if ($this->referenceExists($rowRf)) continue;
                        $reference = $this->_initModule(
                            'Reference',
                            $rowRf,
                            $this->_taxon->taxonID);
                        $reference->writeModel();
                        unset($reference);
                    }
                    // Export only if Block level III has been selected
                    if ($this->_bl > 2) {
	                    // Distribution
	                    // Data can be stored in distribution or distribution_free_text tables
	                    // Data should be written to different files: Distribution for normalised data,
	                    // Description for free text
                    	$this->_writeDistributions(
                    	   $this->_getDistributions($this->_taxon->taxonID),
                    	   'Distribution'
                    	);
                    	$this->_writeDistributions(
                    	   $this->_getDistributions($this->_taxon->taxonID, true),
                    	   'Description'
                    	);
                        // Lifezones
                        $lifezones = $this->_getLifezones(
                    			$this->_taxon->taxonID);
                        foreach ($lifezones as $iLz => $rowLz) {
                        	$lifezone = $this->_initModule(
                                'SpeciesProfile',
                                $rowLz,
                                $this->_taxon->taxonID);
                        	$lifezone->writeModel();
                        	unset($lifezone);
                        }
                        // Additional data
                        $this->_taxon->setDescription();
                        $this->_taxon->setColUrl();
                    }
                }

                $this->_taxon->writeModel();
                unset($this->_taxon);
            }
        }
    }

    public function createMetaXml ()
    {
        $template = new Template($this->_meta, $this->_dir);
        $template->setDelimiter($this->_del);
        // Null character is invalid in xml, replace with empty string
        $this->_sep != chr(0) ? $sep = $this->_sep : $sep = '';
        $template->setSeparator($sep);
        $template->writeFile('meta.xml');
        unset($template);
    }

    public function copyScripts ()
    {
    	self::copyDir(self::basePath() . '/templates/import-scripts', $this->_dir . 'import-scripts');
    }

    public function zipArchive ()
    {
        $zip = new Zip();
        $zip->createArchive($this->_dir, self::getZipArchivePath());
        unset($zip);
    }

    public function archiveExists ()
    {
        if (file_exists(self::getZipArchivePath())) {
            return true;
        }
        return false;
    }

    public static function getColEmlIni ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['col_eml'];
    }

    public static function getColMetaEmlIni ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['col_meta_eml'];
    }

    public static function getWebsiteUrl ()
    {
        $ini = parse_ini_file('config/settings.ini', true);
        if (isset($ini['website']['url'])) {
            $url = $ini['website']['url'];
            if (substr($url, - 1) != '/') {
                return $url . '/';
            }
            return $url;
        }
        return false;
    }

    public static function getReleaseDate() {
        if (isset($_SESSION['monthly'])) {
            return $_SESSION['monthly']['ini']['credits']['release_date'];
        }
        $ini = parse_ini_file(DCAExporter::basePath() . '/config/settings.ini', true);
        if (isset($ini['credits']['release_date'])) {
            return $ini['credits']['release_date'];
        }
        return false;
    }

    public function referenceExists ($reference)
    {
        $a = md5(serialize($reference));
        if (in_array($a, $this->_savedReferences)) {
            return true;
        }
        $this->_savedReferences[] = $a;
        return false;
    }

    public function resetStoredReferences ()
    {
        $this->_savedReferences = array();
    }

}