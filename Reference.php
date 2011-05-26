<?php
require_once 'Interface.php';
class Reference extends DCAExporterAbstract implements DCA_Interface
{
    public $id;
    public $creator;
    public $date;
    public $title;
    public $source;
    public $type;
    public $taxonRemarks;
    public $subject;
    
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
        $fields = array();
        $this->_writeLine($this->_fh, $fields);
    }

    public function writeObject ()
    {
        $fields = array();
        $this->_writeLine($this->_fh, $fields);
    }
}