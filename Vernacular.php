<?php
require_once 'Interface.php';
class Vernacular extends DCAExporterAbstract implements DCA_Interface
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
    
    public function getVernaculars ($taxon_id)
    {
        $stmt = $this->_dbh->prepare(
            'SELECT t4.`name` as vernacular, 
            t2.`name` as language, 
            t3.`name` as country, 
            t6.`authors`, 
            t6.`year`, 
            t6.`title`, 
            t6.`text` 
            FROM `common_name` t1 
            LEFT JOIN `language` AS t2 ON t2.`iso` = t1.`language_iso` 
            LEFT JOIN `country` AS t3 ON t3.`iso` = t1.`country_iso` 
            LEFT JOIN `common_name_element` AS t4 ON t4.`id` = t1.`common_name_element_id` 
            RIGHT JOIN `reference_to_common_name` AS t5 ON t5.`common_name_id` = t1.`id` 
            RIGHT JOIN `reference` AS t6 ON t6.`id` = t5.`reference_id` 
            WHERE t1.`taxon_id` = ?');
        $stmt->setFetchMode(PDO::FETCH_INTO, $this);
        $stmt->execute(array(
            $vernacular->taxonId
        ));
        while ($stmt->fetch()) {
            //echo "$vernacular->taxonId<br>";
            $vernacular->source = $vernacular->getSource();
            $vernacular->writeVernacular();
        }
        //unset($vernacular);
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

    public function writeHeader ()
    {
        $fields = array();
        $this->_writeLine($this->_fh, $fields);
    }
    
}