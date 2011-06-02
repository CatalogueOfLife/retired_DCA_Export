<?php
require_once 'Interface.php';
class Reference extends DCAExporterAbstract implements DCA_Interface
{
    public $taxonID;
    public $title;
    public $creator;
    public $date;
    public $description;
    public $identifier;
    
    const FILE = 'reference.txt';

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

    public function writeHeader ()
    {
        $fields = array(
            'taxonID', 
            'title', 
            'creator', 
            'date', 
            'description', 
            'identifier'
        );
        $this->_writeLine($this->_fh, $fields);
    }

    public function writeModel ()
    {
        $fields = array(
            $this->taxonID, 
            $this->title, 
            $this->creator, 
            $this->date, 
            $this->description, 
            $this->identifier
        );
        $this->_writeLine($this->_fh, $fields);
    }
}