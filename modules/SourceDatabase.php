<?php
/**
 * SourceDatabase
 * 
 * This is a model different from all others, as it does not write the source database metadata
 * to a single text file but to multiple EML files in the eml directory using the Template class. 
 * It does not implement the DCA_Interface.
 * 
 * @author Ruud Altenburg
 */
require_once 'Template.php';
class SourceDatabase
{
    private $_fileName;
    private $_keys = array(
        'id', 
        'name', 
        'abbreviatedName', 
        'groupName', 
        'authorsEditors', 
        'organisation', 
        'contactPerson', 
        'version', 
        'releaseDate', 
        'abstract', 
        'numberOfSpecies', 
        'numberOfInfraspecies', 
        'numberOfSynonyms', 
        'numberOfCommonNames', 
        'totalNumber'
    );
    private $_fields;
    private $_src;
    private $_dest;

    public function __construct (PDO $dbh, $dir, array $fields)
    {
        $this->_dir = $dir;
        $this->_fields = $this->_setFields($fields);
        $this->_fileName = $this->_setFileName();
        $this->_src = $this->_setSource();
        $this->_dest = $this->_setDestination();
        $this->_resetEmlDir();
    }

    private function _setFields ($fields)
    {
        // No source database metadata available; return Species 2000 metadata
        if (empty($fields)) {
            return DCAExporter::$species2000Metadata;
        }
        // Input array contains unexpected keys; throw exception;
        $diff = array_diff($this->_keys, array_keys($fields));
        if (!empty($diff)) {
            throw new Exception(
                'Input array for EML file is incomplete or invalid!' . print_r(
                    $diff));
        }
        return $fields;
    }

    private function _setFileName ()
    {
        if ($this->_fields['id'] != DCAExporter::$species2000Metadata['id']) {
            return 'src_db_' . $this->_fields['id'] . '.eml';
        }
        return 'species_2000_src_db.eml';
    }

    private function _setSource ()
    {
        return dirname(__FILE__) . '/../templates/eml.tpl';
    }

    private function _setDestination ()
    {
        return dirname(__FILE__) . '/../' . $this->_dir . 'eml/';
        ;
    }

    private function _resetEmlDir ()
    {
        $this->_removeDir($this->_dest);
        mkdir($this->_dest);
    }
    
    private function _removeDir ($dir) 
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir") {
                        _removeDir($dir."/".$object); 
                    } else {
                        unlink($dir."/".$object);
                    }
                } 
            } 
            reset($objects); 
            rmdir($dir); 
        } 
    } 

    public function writeEml ()
    {
        $template = new Template($this->_src, $this->_dest);
        $template->decorate($this->_fields);
        $template->writeFile($this->_fileName, 'eml');
        unset($template);
    }
}