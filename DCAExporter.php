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

    private function _setDel ($del)
    {
        if (empty($del)) {
            return ',';
        }
        return $del;
    }

    private function _setSep ($sep)
    {
        if (empty($sep)) {
            return '"';
        }
        return $sep;
    }
    
    private function _setDefaultDelAndSep() {
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
        $result = $this->_getTaxa();
        foreach ($result as $i => $row) {
            $taxon = new Taxon($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            if ($i == 0) {
                $taxon->writeHeader();
            }
            // Decorate taxon with values fetched with getTaxa
            $taxon->decorate(
                $row);
            // Set additional properties
            $taxon->setRank();
            $taxon->setLsid();
            $taxon->setScientificName();
            $taxon->setNameStatus();
            $taxon->setParentId();
            $taxon->setScrutiny();
            // Write to taxa.txt and destroy
            $taxon->writeObject();
            
            // Remaing data is exported only for (infra)species
            if (!$taxon->isHigherTaxon) {
                
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
        return $res ? $res : false;
    }
}