<?php
require_once 'Interface.php';
class Taxon extends DCAExporterAbstract implements DCA_Interface
{
    public $taxonID;
    public $identifier; // LSID; separate
    public $datasetID;
    public $datasetName;
    public $acceptedNameUsageID;
    public $parentNameUsageID; // separate
    public $taxonomicStatus; // separate
    public $taxonRank; // separate
    public $verbatimTaxonRank;
    public $scientificName; // separate
    public $kingdom;
    public $phylum;
    public $class;
    public $order;
    public $superfamily;
    public $family;
    public $genus;
    public $subgenus;
    public $specificEpithet;
    public $infraspecificEpithet;
    public $scientificNameAuthorship;
    public $source;
    public $namePublishedIn;
    public $nameAccordingTo; // scrutiny, separate
    public $modified; // scrutiny date, separate
    public $description; // additional data, separate
    
    public $fields = array(
        'taxonID', 
        'identifier', 
        'datasetID', 
        'datasetName', 
        'acceptedNameUsageID', 
        'parentNameUsageID', 
        'taxonomicStatus', 
        'taxonRank', 
        'verbatimTaxonRank',
        'scientificName', 
        'kingdom', 
        'phylum', 
        'class', 
        'order', 
        'superfamily', 
        'family', 
        'genus', 
        'subgenus', 
        'specificEpithet', 
        'infraspecificEpithet', 
        'scientificNameAuthorship', 
        'source',
        'namePublishedIn',
        'nameAccordingTo', 
        'modified',
        'description'
    );
    
    // Derived values
    public $status;
    public $isHigherTaxon = false;
    public $isSynonym = false;
    
    // Export settings
    const FILE = 'taxa.txt';
    
    // Lookup tables
    public static $higherTaxa = array(
        'kingdom', 
        'phylum', 
        'class', 
        'order',
        'superfamily',
        'family', 
        'genus'
    );
    
    public static $scientificNameStatus = array(
        1 => 'accepted name', 
        2 => 'ambiguous synonym', 
        3 => 'misapplied name', 
        4 => 'provisionally accepted name', 
        5 => 'synonym'
    );

    public function __construct (PDO $dbh, $dir, $sep, $del)
    {
        parent::__construct($dbh, $dir, $sep, $del);
        $this->_fh = $this->_openFileHandler(self::FILE);
    }

    public function __destruct ()
    {
        $this->_closeFileHandler(self::FILE);
    }

    public function init ()
    {
        $this->_createTextFile(self::FILE);
    }

    public function setRank ()
    {
        if (!empty($this->infraspecificEpithet)) {
            $this->taxonRank = 'infraspecies';
            return;
        }
        if (!empty($this->specificEpithet)) {
            $this->taxonRank = 'species';
            return;
        }
        $ranks = array_reverse(self::$higherTaxa);
        foreach ($ranks as $rank) {
            if (!empty($this->$rank)) {
                $this->taxonRank = $rank;
                $this->isHigherTaxon = true;
                return;
            }
        }
    }
    
    public function setScientificName ()
    {
        if ($this->isHigherTaxon) {
            $this->scientificName = $this->{$this->taxonRank};
            return $this->scientificName;
        }
        $this->scientificName = $this->genus . ' ' . $this->specificEpithet;
        if (!empty($this->verbatimTaxonRank)) {
            $this->scientificName .= ' ' . $this->verbatimTaxonRank;
        }
        if (!empty($this->infraspecificEpithet)) {
            $this->scientificName .= ' ' . $this->infraspecificEpithet;
        }
        return $this->scientificName;
    }

    public function setLsid ()
    {
        $query = 'SELECT t1.`resource_identifier` AS LSID 
                  FROM `uri` AS t1  
                  LEFT JOIN `uri_to_taxon` AS t2 ON t1.`id` = t2.`uri_id` 
                  LEFT JOIN `uri_scheme` AS t3 ON t1.`uri_scheme_id` = t3.`id` 
                  WHERE t2.`taxon_id` = ? AND 
                        t3.`scheme` = "lsid"';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $this->taxonID
        ));
        if ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->identifier = $res['LSID'];
            return true;
        }
        return false;
    }

    public function setNameStatus ()
    {
        if (!array_key_exists($this->status, self::$scientificNameStatus)) {
            // Return accepted name for higher taxa
            $this->taxonomicStatus = self::$scientificNameStatus[1];
            return $this->taxonomicStatus;
        }
        $this->taxonomicStatus = self::$scientificNameStatus[$this->status];
        if (!in_array($this->status, array(
            1, 
            4
        ))) {
            $this->isSynonym = true;
        }
        return $this->taxonomicStatus;
    }

    public function setParentId ()
    {
        $query = 'SELECT `parent_id` 
                  FROM `taxon_name_element` 
                  WHERE `taxon_id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            $this->taxonID
        ));
        if ($res = $stmt->fetch(PDO::FETCH_NUM)) {
            $this->parentNameUsageID = $res[0];
            return $this->parentNameUsageID;
        }
        return false;
    }

    public function setScrutiny ()
    {
        if (!$this->isHigherTaxon) {
            $query = 'SELECT t3.`name` AS nameAccordingTo, 
                             t2.`original_scrutiny_date` AS modified
                      FROM `taxon_detail` AS t1 
                      LEFT JOIN `scrutiny` AS t2 ON t1.`scrutiny_id` = t2.`id` 
                      LEFT JOIN `specialist` AS t3 ON t2.`specialist_id` = t3.`id` 
                      WHERE t1.`taxon_id` = ?';
            $stmt = $this->_dbh->prepare($query);
            $stmt->execute(array(
                $this->taxonID
            ));
            if ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->decorate($res);
                return true;
            }
            return false;
        }
    }
    
    public function setDescription () {
        if (!$this->isHigherTaxon) {
            $query = 'SELECT `additional_data` AS description
                      FROM `taxon_detail` 
                      WHERE `taxon_id` = ?';
            $stmt = $this->_dbh->prepare($query);
            $stmt->execute(array(
                $this->taxonID
            ));
            if ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->decorate($res);
                return true;
            }
            return false;
        }
    }

    public function writeModel ()
    {
        $fields = array(
            $this->taxonID, 
            $this->identifier, 
            $this->datasetID, 
            $this->datasetName . ' in ' . $this->_getCredits(), 
            $this->acceptedNameUsageID, 
            $this->parentNameUsageID, 
            $this->taxonomicStatus, 
            $this->taxonRank, 
            $this->verbatimTaxonRank, 
            $this->scientificName, 
            $this->kingdom, 
            $this->phylum, 
            $this->class, 
            $this->order, 
            $this->superfamily, 
            $this->family, 
            $this->genus, 
            $this->subgenus, 
            $this->specificEpithet, 
            $this->infraspecificEpithet, 
            $this->scientificNameAuthorship, 
            $this->source,
            $this->namePublishedIn,
            $this->nameAccordingTo, 
            $this->modified,
            $this->description
        );
        $this->_writeLine($this->_fh, $fields);
    }

}