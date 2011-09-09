<?php
require_once 'Interface.php';
class Distribution extends DCAExporterAbstract implements DCA_Interface
{
    public $taxonID;
    public $locationID;
    public $locality;
    public $occurrenceStatus;
    public $establishmentMeans;
    
    public $fields = array(
        'taxonID', 
        'locationID', 
        'locality', 
        'occurrenceStatus', 
        'establishmentMeans'
    );
    
    const FILE = 'distribution.txt';

    public function __construct (PDO $dbh, $dir, $del, $sep)
    {
        parent::__construct($dbh, $dir, $del, $sep);
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

    public function writeModel ()
    {
        $fields = array(
            $this->taxonID, 
            $this->locationID, 
            $this->locality, 
            $this->occurrenceStatus, 
            $this->establishmentMeans
        );
        $this->_writeLine($this->_fh, $fields);
    }
}