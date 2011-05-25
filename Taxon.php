<?php
class Taxon extends DCAExporterAbstract
{
    public $taxonID;
    public $LSID; // separate
    public $datasetID;
    public $datasetName;
    public $acceptedNameUsageID;
    public $parentNameUsageID; // separate
    public $taxonomicStatus; // separate
    public $taxonRank; // separate
    public $scientificName;
    public $kingdom;
    public $phylum;
    public $class;
    public $order;
    public $family;
    public $genus;
    public $subgenus;
    public $specificEpithet;
    public $infraspecificEpithet;
    public $scientificNameAuthorship;
    public $nameAccordingTo; // scrutiny, separate
    public $modified; // scrutiny date, separate
    

    // Derived values
    public $status;
    private $_isHigherTaxon = false;
    
    // Export settings
    private $_fh;
    const FILE = 'taxa.txt';
    
    // Lookup tables
    public static $higherTaxa = array(
        'kingdom', 
        'phylum', 
        'class', 
        'order', 
        'family', 
        'genus', 
        'subgenus'
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
            return $this->taxonRank;
        }
        ;
        if (!empty($this->specificEpithet)) {
            $this->taxonRank = 'species';
            return $this->taxonRank;
        }
        ;
        $ranks = array_reverse(self::$higherTaxa);
        foreach ($ranks as $rank) {
            if (!empty($this->$rank)) {
                $this->taxonRank = $rank;
                $this->_isHigherTaxon = true;
                return $this->taxonRank;
            }
        }
        return false;
    }

    public function setScientificName ()
    {
        if ($this->_isHigherTaxon) {
            $this->scientificName = $this->{$this->taxonRank};
            return $this->scientificName;
        }
        $this->scientificName = $this->genus . ' ' . $this->specificEpithet;
        if (!empty($this->infraspecificEpithet)) {
            $this->scientificName .= ' ' . $this->infraspecificEpithet;
        }
        return $this->scientificName;
    }

    public function setNameStatus ()
    {
        if (!in_array($this->status, self::$scientificNameStatus)) {
            // Return accepted name for higher taxa
            $this->taxonomicStatus = self::$scientificNameStatus[1];
            return $this->taxonomicStatus;
        }
        $this->taxonomicStatus = self::$scientificNameStatus[$this->status];
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
        if (!$this->_isHigherTaxon) {
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

    public function writeTaxon ()
    {
        $fields = array(
            $this->taxonID, 
            $this->LSID, 
            $this->datasetID, 
            $this->datasetName, 
            $this->acceptedNameUsageID, 
            $this->parentNameUsageID, 
            $this->taxonomicStatus, 
            $this->taxonRank, 
            $this->scientificName, 
            $this->kingdom, 
            $this->phylum, 
            $this->class, 
            $this->order, 
            $this->family, 
            $this->genus, 
            $this->subgenus, 
            $this->specificEpithet, 
            $this->infraspecificEpithet, 
            $this->scientificNameAuthorship, 
            $this->nameAccordingTo, 
            $this->modified
        );
        $this->_writeLine($this->_fh, $fields);
    }

}