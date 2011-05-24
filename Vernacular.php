<?php
class Vernacular extends DCAExporterAbstract
{
    public $taxonId;
    public $vernacular;
    public $language;
    public $country;
    public $source;
    
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

    public function getSource ()
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
        return trim($source);
    }
    
    public function writeVernacular ()
    {
        $fields = array(
            $this->taxonId, 
            $this->vernacular, 
            $this->language, 
            $this->country, 
            $this->source
        );
        $this->_writeLine($this->_fh, $fields);
    }
    
}