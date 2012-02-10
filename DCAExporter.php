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
require_once 'modules/Reference.php';
require_once 'modules/SourceDatabase.php';

class DCAExporter
{
    // Export directory
    public static $dir = 'export/';
    // Name of meta.xml template
    public static $meta = 'templates/meta.tpl';
    // Base name of zip archive
    public static $zip = 'zip/';
    // Metadata for Annual Checklist itself is not stored in the database
    // Set credits in this array
    // TODO: get proper data for this!
    public static $species2000Metadata = array(
        'id' => 'col', 
        'title' => 'Catalogue of Life', 
        'abbreviatedName' => 'Catalogue of Life', 
        'groupName' => '', 
        'authorsEditors' => '', 
        'organizationName' => '', 
        'contact' => '', 
        'version' => '', 
        'pubDate' => '', 
        'abstract' => '', 
        'sourceUrl' => '', 
        'taxonomicCoverage' => '', 
        'contactCountry' => 'GB', 
        'contactCity' => 'Reading', 
        'numberOfSpecies' => '', 
        'numberOfInfraspecies' => '', 
        'numberOfSynonyms' => '', 
        'numberOfCommonNames' => '', 
        'totalNumber' => '', 
        'resourceLogoUrl' => ''
    );
    
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
    
    // Storage array to determine if an eml file has already been written 
    private $_savedEmls = array();
    // Indicator to show progress
    private $_indicator;
    // ignore_user_abort() original setting (will be restored)
    private $_iuaSetting;
    
    // Collects bootstrap errors
    public $startUpErrors;

    public function __construct ($sc, $bl)
    {
        $this->_createDbInstance('db');
        $this->_dbh = DbHandler::getInstance('db');
        $ini = parse_ini_file('config/settings.ini', true);
        $this->_del = $ini['export']['delimiter'];
        $this->_sep = $ini['export']['separator'];
        $this->_sc = self::filterSc($sc);
        $this->_bl = $bl;
        $this->_dir = self::basePath() . '/' . self::$dir . md5(self::getZipArchiveName()) . '/';
        $this->_meta = self::basePath() . '/' . self::$meta;
        $this->_zip = self::basePath() . '/' . self::$zip;
        $this->_setDefaults();
        $this->_iuaSetting = ignore_user_abort(1);

        $bootstrap = new DCABootstrap($this->_dbh, $this->_del, $this->_sep, $this->_sc, 
            $this->_bl, $this->_dir, $this->_zip);
        $this->startUpErrors = $bootstrap->getErrors();
        unset($bootstrap);
    }
    
    public function __destruct ()
    {
        $this->deleteTempDir();
        // Reset ignore_user_abort back to 0 if this was set as such in php.ini
        if (!$this->_iuaSetting) {
            ignore_user_abort(0);
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
        $ini = parse_ini_file('config/settings.ini', true);
        return $ini['credits']['string'] . ' (' . $ini['credits']['release_date'] . ')';
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
    
    public static function getZipArchiveName ()
    {
        $sc = self::filterSc($_REQUEST);
        $url = 'archive-';
        if (in_array('[all]', $sc)) {
            return $url . 'complete.zip';
        }
        foreach ($sc as $rank => $taxon) {
            $url .= strtolower($rank) . '-' . strtolower($taxon) . '-';
        }
        $url .= 'bl' . $_REQUEST['block'] . '.zip';
        return $url;
    }
    
    public static function getZipArchivePath ()
    {
        return self::basePath() . '/'. self::$zip . self::getZipArchiveName ();
    }
    
    public static function basePath ()
    {
        return dirname(__FILE__);
        
    }
    
    public static function getPreviousEditions ()
    {
        // Previous editions should be named yyyy-mm-dd-archive-complete.zip
        // and stored in the 'zip-fixed' directory
        $files = array();
        $dir = 'zip-fixed';
        $path = $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        $d = dir($dir);
        while (false !== ($file = $d->read())) {
            if (is_numeric(substr($file, 0, 4)) && !is_dir($file)) {
                list($year, $month, $day) = explode('-', $file);
                $files[] = array(
                    'edition' => date("j F Y", mktime(0, 0, 0, $month, $day, $year)),
                    'size' => self::getDownloadSize($dir .'/' . $file),
                    'url' => 'http://' . substr($path, 0, -(strlen(basename($path)))). $dir . '/' . $file
                );
            }
        }
        $d->close();
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
        // Change search all option [all] to MySQL wildcard
        foreach ($this->_sc as $k => $v) {
            if ($v == '[all]') {
                $this->_sc[$k] = '%';
            }
        }
    }

    private function _initModel ($model, array $data, $taxonId = null)
    {
        try {
            $model = new $model($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            if ($taxonId) {
                $model->taxonID = $taxonId;
            }
            $model->decorate($data);
        }
        catch (Exception $e) {
            return $e;
        }
        return $model;
    }

    private function _getTaxa ($limit, $offset)
    {
        $query = 'SELECT `id` AS taxonID,
                          IF (`source_database_id` > 0,
                              `source_database_id`,
                              "Species 2000") AS datasetID,
                          IF (`source_database_name` != "", 
                              `source_database_name`, 
                              "Catalogue of Life") AS datasetName,
                         `kingdom`,
                         `phylum`,
                         `class`,
                         `order`,
                         `superfamily`,
                         `family`,
                         `genus`,';
        // Split query based on block level; level 1 only exports classification up to genus
        if ($this->_bl == 1) {
            $query .= 
                 '"" AS acceptedNameUsageID,
                  "" AS status,
                  "" AS subgenus,
                  "" AS specificEpithet,
                  "" AS infraspecificEpithet,
                  "" AS verbatimTaxonRank,
                  "" AS scientificNameAuthorship ';
        } else {
            $query .= 
                 'IF (`accepted_species_id` > 0,
                      `accepted_species_id`,
                      "") AS acceptedNameUsageID,
                 `status`,
                  "" AS subgenus,
                 `species` AS specificEpithet,
                 `infraspecies` AS infraspecificEpithet,
                 `infraspecific_marker` AS verbatimTaxonRank,
                 `author` AS scientificNameAuthorship ';
        }
        $query .= ' FROM `_search_scientific` ';
        if (!empty($this->_sc)) {
            $query .= 'WHERE ';
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` LIKE :$field AND ";
            }
            // Omit (infra)species for level 1
            if ($this->_bl == 1) {
                $query .= ' `species` = "" AND `infraspecies` = "" AND ';
            }
            $query = substr($query, 0, -4);
        }
        $query .= ' LIMIT :limit OFFSET :offset';
        $stmt = $this->_dbh->prepare($query);
        if (!empty($this->_sc)) {
            foreach ($this->_sc as $field => $value) {
                $stmt->bindValue(':' . $field, $value . '%');
            }
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }
    
    private function _getVernaculars ($taxon_id)
    {
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

    private function _getDistributions ($taxon_id, $distributionFreeText = false)
    {
        if (!$distributionFreeText) {
            $query = 'SELECT t2.`status` AS occurrenceStatus,
                         t3.`original_code` AS locationID,
                         t3.`name` AS locality,
                         "" AS establishmentMeans
                      FROM `distribution` AS t1 
                      LEFT JOIN `distribution_status` AS t2 ON t1.`distribution_status_id` = t2.`id` 
                      LEFT JOIN `region` AS t3 ON t1.`region_id` = t3.`id`
                      WHERE t1.`taxon_detail_id` = ?';
        }
        else {
            $query = 'SELECT "" AS occurrenceStatus,
                         "" AS locationID,
                         t2.`free_text` AS locality,
                         "" AS establishmentMeans
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
                    t1.`contact_person` AS contact,
                    "" AS sourceUrl,
                    "" AS contactCity,
                    "" AS contactCountry,
                    "" AS resourceLogoUrl
                  FROM `source_database` t1
                  WHERE t1.`id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $source_database_id
        ));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _initEml ()
    {
        // Initialize with CoL metadata, so archive always contains this EML file
        $sp2000 = new SourceDatabase($this->_dbh, $this->_dir);
        // Clear dir from previous export first
        $sp2000->resetEmlDir();
        $sp2000->writeEml();
        unset($sp2000);
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
        $params = array();
        $query = 'SELECT COUNT(`id`)
                  FROM `_search_scientific` ';
        if (!empty($this->_sc)) {
            $query .= 'WHERE ';
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` LIKE ? AND ";
                $params[] = $value.'%';
            }
            if ($this->_bl == 1) {
                $query .= ' `species` = "" AND `infraspecies` = "" AND ';
            }
            $query = substr($query, 0, -4);
        }
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_NUM);
        return $res ? $res[0] : false;
    }

    public function useIndicator ()
    {
        $this->_indicator = new Indicator();
    }

    public function writeData ()
    {
        $total = $this->getTotalNumberOfTaxa();
        $this->_indicator ? $this->_indicator->init($total, 75, 50) : '';
        $this->_initEml();
        
        for ($limit = 1000, $offset = 0; $offset < $total; $offset += $limit) {
            $taxa = $this->_getTaxa($limit, $offset);
            foreach ($taxa as $iTx => $rowTx) {
                $this->_indicator ? $this->_indicator->iterate() : '';
                $taxon = $this->_initModel('Taxon', $rowTx);
                $taxon->setRank();
                $taxon->setNameStatus();
                $taxon->setScientificName();
                $taxon->setLsid();
                $taxon->setParentId();
                
                if (!$this->_emlExists($taxon->datasetID)) {
                    $sourceDatabase = new SourceDatabase(
                        $this->_dbh, 
                        $this->_dir, 
                        $this->_getSourceDatabaseMetadata(
                            $taxon->datasetID));
                    $sourceDatabase->writeEml();
                    unset($sourceDatabase);
                    $this->_addSavedEml($taxon->datasetID);
                }
                
                // Remaining data is exported only for (infra)species
                // and for Block levels II to IV
                if (!$taxon->isHigherTaxon && $this->_bl > 1) {
                    $taxon->setScrutiny();
                    $taxon->setGsdNameGuid();
                    
                    // Vernaculars
                    $vernaculars = $this->_getVernaculars($taxon->taxonID);
                    foreach ($vernaculars as $iVn => $rowVn) {
                        $vernacular = $this->_initModel(
                            'Vernacular', 
                            $rowVn, 
                            $taxon->taxonID);
                        $vernacular->setSource();
                        $vernacular->writeModel();
                        // Vernacular references
                        $references = $this->_getReferences(
                            $vernacular->vernacularID, 
                            'common_name');
                        foreach ($references as $iRf => $rowRf) {
                            $reference = $this->_initModel(
                                'Reference', 
                                $rowRf, 
                                $taxon->taxonID);
                            $reference->writeModel();
                            unset($reference);
                        }
                        unset($vernacular);
                    }
                    
                    // Taxon/ synonym references
                    !$taxon->isSynonym ? $type = 'taxon' : $type = 'synonym';
                    $references = $this->_getReferences(
                        $taxon->taxonID, 
                        $type);
                    foreach ($references as $iRf => $rowRf) {
                        $reference = $this->_initModel(
                            'Reference', 
                            $rowRf, 
                            $taxon->taxonID);
                        $reference->writeModel();
                        unset($reference);
                    }
                    // Distribution
                    // Data can be stored in distribution or distribution_free_text tables
                    // Try distribution first; if empty do second query on distribution_free_text
                    // Export only if Block level III has been selected
                    if ($this->_bl > 2) {
                        $distributions = $this->_getDistributions(
                            $taxon->taxonID);
                        if (empty(
                            $distributions)) {
                            $distributions = $this->_getDistributions(
                                $taxon->taxonID, 
                                true);
                        }
                        foreach ($distributions as $iDs => $rowDs) {
                            $distribution = $this->_initModel(
                                'Distribution', 
                                $rowDs, 
                                $taxon->taxonID);
                            $distribution->writeModel();
                            unset(
                                $distribution);
                        }
                            $taxon->setDescription();
                    }
                    
                    // Block IV only adds additional data to the taxon
                    if ($this->_bl ==
                         4) {
                            $taxon->setDescription();
                    }
                }
                
                $taxon->writeModel();
                unset($taxon);
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
}