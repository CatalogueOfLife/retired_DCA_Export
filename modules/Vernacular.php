<?php
require_once 'Interface.php';
class Vernacular extends DCAModuleAbstract implements DCAModuleInterface
{
    public $taxonID;
    public $vernacularName;
    public $language;
    public $countryCode;
    public $locality;
    public $transliteration;
    public $vernacularID; // use to store associated references
    
    public $fields = array(
        'taxonID', 
        'vernacularName', 
        'language', 
        'countryCode', 
        'locality', 
        'transliteration'
    );

    // Derived properties
    public $authors;
    public $year;
    public $title;
    public $text;
    
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
            $this->text
        );
        foreach ($elements as $element) {
            if ($element != '') {
                $source .= $element . '. ';
            }
        }
        $this->source = trim($source);
        return $this->source;
    }

    public function writeModel ()
    {
        $fields = array(
            $this->taxonID, 
            $this->vernacularName, 
            $this->language, 
            $this->countryCode, 
            $this->locality, 
            $this->transliteration
        );
        
        $this->_writeLine($this->_fh, $fields);
    }
}