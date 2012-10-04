<?php
require_once 'Interface.php';
class SpeciesProfile extends DCAModuleAbstract implements DCAModuleInterface
{
    public $taxonID;
    public $habitat;
    
    public $fields = array(
        'taxonID', 
        'habitat'
    );

    const FILE = 'speciesprofile.txt';

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
            $this->habitat
        );
        $this->_writeLine($this->_fh, $fields);
    }
}