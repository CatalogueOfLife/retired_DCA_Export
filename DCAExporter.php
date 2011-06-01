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

class DCAExporter
{
    // Database handler
    private $_dbh;
    // Export directory
    private $_dir;
    // Text file delimiter
    private $_del;
    // Text file separator
    private $_sep;
    // Search criteria
    private $_sc;
    // Block level; determines amount of data returned
    private $_bl;
    
    public $ini;
    public $startUpErrors;
    private $_indicator;

    public 

function __construct ($sc, $bl)
{
    $this->ini = parse_ini_file('config/settings.ini', true);
    $this->_dir = $this->ini['export']['export_dir'];
    $this->_del = $this->ini['export']['delimiter'];
    $this->_sep = $this->ini['export']['separator'];
    $this->_sc = $sc;
    $this->_bl = $bl;
    $this->_setDefaults();
    
    $bootstrap = new Bootstrap($this->_dir, $this->_del, $this->_sep, $this->_sc, $this->_bl);
    $this->startUpErrors = $bootstrap->getErrors();
    // print_r($this->startUpErrors);
    $this->_dbh = $bootstrap->getDbHandler();
    unset($bootstrap);
}

    public function getStartUpErrors ()
    {
        return $this->startUpErrors;
    }

    public function archiveExists ()
    {
        if (file_exists($this->_getZipArchiveName())) {
            return true;
        }
        return false;
    }

    public function useIndicator ()
    {
        $this->_indicator = new Indicator();
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
            $this->_del = chr(9);
            $this->_sep = chr(0);
        }
        // Change search all option [all] to MySQL wildcard
        foreach ($this->_sc as $k => $v) {
            if ($v == '[all]') {
                $this->_sc[$k] = '%';
            }
        }
    }

    public function getExportSettings ()
    {
        return $this->ini['export'];
    }

    public function writeData ()
    {
        $total = $this->getTotalNumberOfTaxa();
        $this->_indicator ? $this->_indicator->init($total, 75, 50) : '';
        
        for ($limit = 1000, $offset = 0; $offset < $total; $offset += $limit) {
            $taxa = $this->_getTaxa($limit, $offset);
            foreach ($taxa as $iTx => $rowTx) {
                $this->_indicator ? $this->_indicator->iterate() : '';
                $taxon = $this->_initModel('Taxon', $rowTx);
                $taxon->setRank();
                $taxon->setLsid();
                $taxon->setScientificName();
                $taxon->setNameStatus();
                $taxon->setParentId();
                $taxon->setScrutiny();
                $taxon->writeModel();
                
                // Remaing data is exported only for (infra)species
                // and for Block levels II and III
                if (!$taxon->isHigherTaxon && $this->_bl > 1) {
                    // Vernaculars
                    $vernaculars = $this->_getVernaculars(
                        $taxon->taxonID);
                    foreach ($vernaculars as $iVn => $rowVn) {
                        $vernacular = $this->_initModel(
                            'Vernacular', 
                            $rowVn, 
                            $taxon->taxonID);
                        $vernacular->setSource();
                        $vernacular->writeModel();
                        unset($vernacular);
                    }
                    // References
                    $references = $this->_getReferences(
                        $taxon->taxonID, 
                        $taxon->isSynonym);
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
                        if (empty($distributions)) {
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
                            unset($distribution);
                        }
                    }
                }
                unset($taxon);
            }
        }
    }

    private function _initModel ($model, $data, $taxonId = null)
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

    public function createMetaXml ()
    {
        $src = dirname(__FILE__) . '/templates/meta.tpl';
        $dest = dirname(__FILE__) . '/' . $this->_dir;
        
        $template = new Template($src, $dest);
        $template->setDelimiter($this->_del);
        $template->setSeparator($this->_sep);
        $template->writeFile('meta.xml');
        unset($template);
    }

    public function zipArchive ()
    {
        $src = dirname(__FILE__) . '/' . $this->_dir;
        // Default name of archive is archive-rank-taxon.zip
        $dest = $this->_getZipArchiveName();
        $zip = new Zip();
        $zip->createArchive($src, $dest);
        unset($zip);
    }
    
    private function _getZipArchiveName() {
        return dirname(__FILE__) . '/' . 
               $this->ini['export']['zip_archive'] . '-' . 
               array_shift(array_keys($this->_sc)) . '-' . 
               array_shift(array_values($this->_sc)) . '-bl' .
               $this->_bl . '.zip';
    }

    public function getTotalNumberOfTaxa ()
    {
        $params = array();
        $query = 'SELECT COUNT(`id`)
                  FROM `_search_scientific` ';
        // @TODO: extend this for other search criteria!
        if (!empty($this->_sc)) {
            $query .= 'WHERE ';
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` LIKE ? AND ";
                $params[] = $value;
            }
            $query = substr($query, 0, -4);
        }
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_NUM);
        return $res ? $res[0] : false;
    }

    private function _getTaxa ($limit, $offset)
    {
        $query = 'SELECT `id` AS taxonID,
                          IF (`source_database_id` > 0,
                              `source_database_id`,
                              "") AS datasetID,
                          IF (`source_database_name` != "", 
                              `source_database_name`, 
                              "Catalogue of Life") AS datasetName,
                          IF (`accepted_species_id` > 0,
                              `accepted_species_id`,
                              "") AS acceptedNameUsageID,
                         `status`,
                         `kingdom`,
                         `phylum`,
                         `class`,
                         `order`,
                         `family`,
                         `genus`,
                         `subgenus`,
                         `species` AS specificEpithet,
                         `infraspecies` AS infraspecificEpithet,
                         `author` AS scientificNameAuthorship
                  FROM `_search_scientific` ';
        // @TODO: extend this for other search criteria!
        if (!empty($this->_sc)) {
            $query .= 'WHERE ';
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` LIKE :$field AND ";
            }
            $query = substr($query, 0, -4);
        }
        $query .= 'LIMIT :limit OFFSET :offset';
        $stmt = $this->_dbh->prepare($query);
        if (!empty($this->_sc)) {
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

    private function _getVernaculars ($taxon_id)
    {
        $query = 'SELECT t3.`name` as vernacularName, 
                          t2.`name` as language, 
                          t1.`country_iso` as countryCode, 
                          t6.`name` AS locality, 
                          t5.`authors`, 
                          t5.`year`, 
                          t5.`title`, 
                          t5.`text` 
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

    private function _getReferences ($taxon_id, $isSynonym = false)
    {
        $type = 'taxon';
        if ($isSynonym) {
            $type = 'synonym';
        }
        $query = 'SELECT t1.`title`,
                         t1.`authors` AS creator,
                         t1.`year` AS date,
                         t1.`text` AS description,
                         t2.`resource_identifier` AS identifier
                  FROM `reference` t1 
                  LEFT JOIN `uri` AS t2 ON t1.`uri_id` = t2.`id`
                  LEFT JOIN `reference_to_' . $type . '` AS t3 ON t1.`id` = t3.`reference_id` 
                  WHERE t3.`' . $type . '_id` = ?';
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
}