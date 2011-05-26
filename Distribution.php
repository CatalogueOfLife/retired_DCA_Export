<?php
require_once 'Interface.php';
class Distribution extends DCAExporterAbstract implements DCA_Interface
{
    public $id;
    public $locality;
    public $occurrenceStatus;
    
    const FILE = 'distribution.txt';

    public function __construct(PDO $dbh, $dir, $del, $sep) {
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
    

    public function writeHeader ()
    {
        $fields = array();
        $this->_writeLine($this->_fh, $fields);
    }

    public function writeObject ()
    {
        $fields = array();
        $this->_writeLine($this->_fh, $fields);
    }
}