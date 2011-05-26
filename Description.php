<?php
require_once 'Interface.php';
class Description extends DCAExporterAbstract implements DCA_Interface
{
    public $id;
    public $type;
    public $description;
    
    const FILE = 'description.txt';

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
}