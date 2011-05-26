<?php
require_once 'DbHandler.php';
require_once 'Bootstrap.php';
require_once 'Abstract.php';
require_once 'Taxon.php';
require_once 'Vernacular.php';
require_once 'Description.php';
require_once 'Distribution.php';
require_once 'Identifier.php';
require_once 'Reference.php';
require_once 'Indicator.php';
require_once 'Zip.php';
require_once 'Template.php';

class DCAExporter
{
    private $_ini;
    private $_dbh;
    private $_dir;
    private $_del;
    private $_sep;
    private $_sc;

    public function __construct ($sc)
    {
        $this->_setIni();
        $this->_setDbInst();
        
        $this->_dbh = DbHandler::getInstance('db');
        $this->_dir = $this->_ini['export']['export_dir'];
        $this->_del = $this->_ini['export']['delimiter'];
        $this->_sep = $this->_ini['export']['separator'];
        $this->_sc = $sc;
        
        $this->_setDefaultDelAndSep();
        
        $bootstrap = new Bootstrap($this->_dbh, $this->_dir, $this->_del, $this->_sep, $this->_sc);
        unset($bootstrap);
    }

    private function _setIni ()
    {
        $this->_ini = parse_ini_file('config/settings.ini', true);
    }

    private function _setDefaultDelAndSep ()
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
    }

    private function _setDbInst ()
    {
        $config = $this->_ini['db'];
        $dbOptions = array();
        if (isset($config["options"])) {
            $options = explode(",", $config["options"]);
            foreach ($options as $option) {
                $pts = explode("=", trim($option));
                $dbOptions[$pts[0]] = $pts[1];
            }
            DbHandler::createInstance('db', $config, $dbOptions);
        }
    }

    public function writeTaxa ()
    {
        $taxa = $this->_getTaxa();
        foreach ($taxa as $iTx => $rowTx) {
            $taxon = new Taxon($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            // Decorate taxon with values fetched with getTaxa
            $taxon->decorate($rowTx);
            // Set additional properties
            $taxon->setRank();
            $taxon->setLsid();
            $taxon->setScientificName();
            $taxon->setNameStatus();
            $taxon->setParentId();
            $taxon->setScrutiny();
            $taxon->writeObject();
            
            // Remaing data is exported only for (infra)species
            if (!$taxon->isHigherTaxon) {
                $vernaculars = $this->_getVernaculars($taxon->taxonID);
                foreach ($vernaculars as $iVn => $rowVn) {
                    $vernacular = new Vernacular($this->_dbh, 
                        $this->_dir, 
                        $this->_del, 
                        $this->_sep);
                    $vernacular->decorate($rowVn);
                }
            }
            
            /*                
            echo '<pre>';
            print_r($taxon);
            echo '</pre>';
*/
            unset($taxon);
        }
    }

    public function createMetaXml ()
    {
        $src = dirname(__FILE__) . '/template/meta.tpl';
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
        $dest = dirname(__FILE__) . '/' . $this->_ini['export']['zip_archive'];
        
        $zip = new Zip();
        $zip->createArchive($src, $dest);
        unset($zip);
    }

    private function _getTaxa ()
    {
        $params = array();
        $query = 'SELECT `id` AS taxonID,
                         `source_database_id` AS datasetID,
                          IF (`source_database_name` != "", 
                             `source_database_name`, 
                             "Catalogue of Life"
                          ) AS datasetName,
                         `accepted_species_id` AS acceptedNameUsageID,
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
                $query .= "`$field` LIKE ? AND ";
                $params[] = $value;
            }
            $query = substr($query, 0, -4);
        }
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

    private function _getVernaculars ($taxon_id)
    {
        $query = 'SELECT t3.`name` as vernacularName, 
                          t2.`name` as language, 
                          t1.`country_iso` as countryCode, 
                          t5.`authors`, 
                          t5.`year`, 
                          t5.`title`, 
                          t5.`text` 
                   FROM `common_name` t1 
                   LEFT JOIN `language` AS t2 ON t2.`iso` = t1.`language_iso` 
                   LEFT JOIN `common_name_element` AS t3 ON t3.`id` = t1.`common_name_element_id` 
                   RIGHT JOIN `reference_to_common_name` AS t4 ON t4.`common_name_id` = t1.`id` 
                   RIGHT JOIN `reference` AS t5 ON t5.`id` = t4.`reference_id` 
                   WHERE t1.`taxon_id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $taxon_id
        ));
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : array();
    }

}