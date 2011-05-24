<?php
class Taxon extends DCAExporterAbstract
{
    public $id;
    public $scientificName;
    public $genus;
    public $specificEpithet;
    public $infraspecificEpithet;
    public $authorship;
    public $parentId;
    public $acceptedId;
    public $taxonConceptId;
    public $rank;
    public $taxonomicStatus;
    public $accordingTo;
    public $dcModified;
    public $datasetId;
    public $datasetName;
    public $source;
    public $bibliographicCitation;
    
    // Derived properties, used to look up values
    public $infraspecificMarker;
    public $speciesId;
    public $genusId;
    public $speciesLsid;
    public $infraspeciesLsid;
    
    private $_fh;
    const FILE = 'taxon.txt';

    public function __construct(PDO $dbh, $dir, $sep, $del) {
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

    public function getRank ()
    {
        if ($this->infraspecificEpithet == '') {
            return 'species';
        }
        $stmt = $this->_dbh->prepare(
            'SELECT t2.`rank` 
            FROM `taxon` t1, 
            `taxonomic_rank` t2 
            WHERE t1.`id` = ? 
            AND t1.`taxonomic_rank_id` = t2.`id`');
        $stmt->execute(array(
            $this->id
        ));
        $rank = $stmt->fetchColumn(0);
        if ($rank == '') {
            $rank = 'infraspecies';
        }
        return $rank;
    }

    public function getScientificName ()
    {
        $scientificName = trim($this->genus) . ' ' . trim($this->specificEpithet);
        if ($this->rank != 'species') {
            if ($this->infraspecificMarker != '') {
                $scientificName .= ' ' . $this->infraspecificMarker;
            }
            $scientificName .= ' ' . trim($this->infraspecificEpithet);
        }
        return $scientificName;
    }

    public function getParentId ()
    {
        if ($this->rank != 'species') {
            return $this->speciesId;
        }
        return $this->genusId;
    }

    public function getTaxonConceptId ()
    {
        if ($this->rank != 'species') {
            return $this->infraspeciesLsid;
        }
        return $this->speciesLsid;
    }
    
    public function writeTaxon ()
    {
        $fields = array(
            $this->id, 
            $this->scientificName, 
            $this->genus, 
            $this->specificEpithet, 
            $this->infraspecificEpithet, 
            $this->authorship, 
            $this->parentId, 
            $this->acceptedId, 
            $this->taxonConceptId, 
            $this->rank, 
            $this->taxonomicStatus, 
            $this->accordingTo, 
            $this->dcModified, 
            $this->datasetId, 
            $this->datasetName, 
            $this->source, 
            $this->bibliographicCitation
        );
        $this->_writeLine($this->_fh, $fields);
    }

}