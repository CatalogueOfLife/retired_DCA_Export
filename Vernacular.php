<?php
require_once 'Interface.php';
class Vernacular extends DCAExporterAbstract implements DCA_Interface
{
    public $taxonId;
    public $vernacularName;
    public $language;
    public $countryCode;
    public $locality;
    public $source;
    
    // Derived properties
    public $authors;
    public $year;
    public $title;
    public $text;
    
    private $_vernaculars = array();
    const FILE = 'vernacular.txt';

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

    public function setSource ()
    {
        $source = '';
        $elements = array(
            $this->authors, 
            $this->year, 
            $this->title, 
            $this->$text
        );
        foreach ($elements as $element) {
            if ($element != '') {
                $source .= $element . '. ';
            }
        }
        $this->source = trim($source);
        return $this->source;
    }

    public function writeHeader ()
    {
        $fields = array(
            'taxonId', 
            'vernacularName', 
            'language', 
            'countryCode', 
            'locality', 
            'source'
        );
        $this->_writeLine($this->_fh, $fields);
    }

    public function writeObject ()
    {
        $fields = array(
            $this->taxonId, 
            $this->vernacularName, 
            $this->language, 
            $this->countryCode, 
            $this->locality, 
            $this->source
        );
        
        $this->_writeLine($this->_fh, $fields);
    }
}