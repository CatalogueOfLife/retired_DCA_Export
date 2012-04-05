<?php
require_once 'Interface.php';
class Reference extends DCAModuleAbstract implements DCAModuleInterface
{
    public $taxonID;
    public $creator;
    public $date;
    public $title;
    public $description;
    public $identifier;
    public $type;
    
    public $fields = array(
        'taxonID', 
        'creator', 
        'date', 
        'title', 
        'description', 
        'identifier',
        'type'
    );

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

    public function writeModel ()
    {
        $fields = array(
            $this->taxonID, 
            $this->creator, 
            $this->date, 
            $this->title, 
            $this->description, 
            $this->identifier,
            $this->type
        );
        $this->_writeLine($this->_fh, $fields);
    }
}