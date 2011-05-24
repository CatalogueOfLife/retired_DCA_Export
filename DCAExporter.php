<?php
require_once 'Bootstrap.php';
require_once 'Abstract.php';
require_once 'Taxon.php';
require_once 'Vernacular.php';
require_once 'Description.php';
require_once 'Distribution.php';
require_once 'Identifier.php';
require_once 'Reference.php';

class DCAExporter
{
    private $_dbh;
    private $_dir;
    private $_del;
    private $_sep;
    private $_sc;
    
    // Names of denormalized tables that may still be subject to change
    const SEARCH_ALL = '_search_all';
    const SPECIES_DETAILS = '_species_details';

    public function __construct (PDO $dbh, $dir, $del, $sep, $sc)
    {
        $bootstrap = new Bootstrap($dbh, $dir, $del, $sep, $sc);
        $this->_dbh = $dbh;
        $this->_dir = $dir;
        $this->_del = $del;
        $this->_sep = $sep;
        $this->_sc = $sc;
        unset($bootstrap);
    }

    public function writeTaxa ()
    {
        // source, bibliographicCitation?
        

        // Get all relevant ids first by querying 'search all'
        $result = $this->_getIds();
        //print_r($results);
        foreach ($result as $row) {
            $taxon = new Taxon($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            // Higher taxon
            if ($this->_isHigherTaxon($row['rank'])) {
                $taxon = $this->_getHigherTaxonDetails ($row['id'], $taxon);
            } else {
                $taxon = $this->_getSpeciesDetails ($row['id'], $taxon);
                $taxon->rank = $taxon->getRank();
                $taxon->scientificName = $taxon->getScientificName();
                $taxon->parentId = $taxon->getParentId();
                $taxon->taxonConceptId = $taxon->getTaxonConceptId();
            }

            
            $taxon->writeTaxon();
            $this->writeVernaculars($taxon->id);
                
                //print_r($taxon);
                
            unset ($taxon);
        }
        
    }

    private function _getIds ()
    {
        $params = array();
        $query = 'SELECT DISTINCT `id`, `rank` 
                  FROM `' . self::SEARCH_ALL . '` ';
        // @TODO: extend this for other search criteria
        if (!empty($this->_sc)) {
            $query .= 'WHERE ';
            foreach ($this->_sc as $field => $value) {
                $query .= "`$field` LIKE ? AND ";
                $params[] = $value;
            }
            $query = substr($query, 0, -4);
        }
        $query .= 'ORDER BY `name`';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function _isHigherTaxon ($rank)
    {
        $higherTaxa = array(
            'kingdom', 
            'phylum', 
            'class', 
            'order', 
            'family', 
            'genus'
        );
        if (in_array(strlolower($rank), $higherTaxa)) {
            return true;
        }
        return false;
    }

    private function _getHigherTaxonDetails ($id, $taxon)
    {
    
    }

    private function _getSpeciesDetails ($id, $taxon)
    {
        $query = 'SELECT `taxon_id` AS id, 
                 `genus_name` AS genus, 
                 `species_name` AS specificEpithet, 
                 `infraspecies_name` AS infraspecificEpithet, 
                 `infraspecific_marker` AS infraspecificMarker, 
                 `author` AS authorship, 
                 "" AS acceptedId, 
                 `status` AS taxonomicStatus, 
                 `specialist` AS accordingTo, 
                 `scrutiny_date` AS dcModified, 
                 `source_database_id` AS datasetId, 
                 `source_database_short_name` AS datasetName, 
                 `species_id` AS speciesId, 
                 `genus_id` AS genusId,
                 `species_lsid` AS speciesLsid, 
                 `infraspecies_lsid` AS infraspeciesLsid  
                 FROM ' . self::SPECIES_DETAILS . ' 
                 WHERE `taxon_id` = ?';
        $stmt->setFetchMode(PDO::FETCH_INTO, $taxon);
        $stmt->execute(array(
            $id
        ));
        return $taxon;
    }

    public function writeVernaculars ($taxon_id)
    {
        $vernacular = new Vernacular($this->_dbh, $this->_dir, $this->_del, $this->_sep);
        $vernacular->taxonId = $taxon_id;
        
        print_r($vernacular);
        
        $stmt = $this->_dbh->prepare(
            'SELECT t4.`name` as vernacular, 
            t2.`name` as language, 
            t3.`name` as country, 
            t6.`authors`, 
            t6.`year`, 
            t6.`title`, 
            t6.`text` 
            FROM `common_name` t1 
            LEFT JOIN `language` AS t2 ON t2.`iso` = t1.`language_iso` 
            LEFT JOIN `country` AS t3 ON t3.`iso` = t1.`country_iso` 
            LEFT JOIN `common_name_element` AS t4 ON t4.`id` = t1.`common_name_element_id` 
            RIGHT JOIN `reference_to_common_name` AS t5 ON t5.`common_name_id` = t1.`id` 
            RIGHT JOIN `reference` AS t6 ON t6.`id` = t5.`reference_id` 
            WHERE t1.`taxon_id` = ?');
        $stmt->setFetchMode(PDO::FETCH_INTO, $vernacular);
        $stmt->execute(array(
            $vernacular->taxonId
        ));
        while ($stmt->fetch()) {
            echo "$vernacular->taxonId<br>";
            $vernacular->source = $vernacular->getSource();
            $vernacular->writeVernacular();
        }
        unset($vernacular);
    }

    public function getGenera ()
    {
    
    }

    public function getHigherTaxa ()
    {
    
    }

    public function writeSynonyms ()
    {
    
    }
}