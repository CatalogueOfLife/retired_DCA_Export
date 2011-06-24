<?php
require_once 'Interface.php';
class Distribution extends DCAExporterAbstract implements DCA_Interface
{
    public $taxonID;
    public $occurrenceStatus;
    public $locationID;
    public $locality;
    public $establishmentMeans;
    
    public $fields = array(
        'taxonID', 
        'occurrenceStatus', 
        'locationID', 
        'locality', 
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
            $this->occurrenceStatus, 
            $this->locationID, 
            $this->locality, 
            $this->establishmentMeans
        );
        $this->_writeLine($this->_fh, $fields);
    }
}