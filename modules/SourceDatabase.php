<?php
/**
 * SourceDatabase
 * 
 * This is a model different from all others, as it does not write the source database metadata
 * to a single text file but to multiple EML files in the eml directory using the Template class. 
 * It does not implement the DCAModuleInterface.
 * 
 * @author Ruud Altenburg
 */
class SourceDatabase
{
    private $_fileName;
    private $_keys = array(
        'id', 
        'title', 
        'abbreviatedName',
        'groupName', 
        'authorsEditors', 
        'contact', 
        'version', 
        'pubDate', 
        'abstract',
        'sourceUrl',
        'contactCountry',
        'contactCity',
        'resourceLogoUrl'
    );
    private $_fields;
    private $_src;
    private $_dest;

    public function __construct (PDO $dbh, $dir, $fields = array())
    {
        $this->_dir = $dir;
        $this->_fields = $this->_setFields($fields);
        $this->_fileName = $this->_setFileName();
        $this->_src = $this->_setSource();
        $this->_dest = $this->_setDestination();
    }

    private function _setFields (array $fields)
    {
        // No source database metadata available; return Species 2000 metadata
        if (empty($fields)) {
            $fields = DCAExporter::$species2000Metadata;
        } else {
            $diff = array_diff($this->_keys, array_keys($fields));
            // Input array contains unexpected keys; throw exception;
            if (!empty($diff)) {
                throw new Exception(
                    'Input array for EML file is incomplete or invalid!' . print_r(
                        $diff));
            }
        }
        $fields['dateStamp'] = date("c"); 
        $fields['pubDate'] = DCAExporter::getReleaseDate();
        $fields['packageId'] = $fields['id'] . ':' . date("Y-m-d");
        $fields['abstract'] = htmlspecialchars($fields['abstract'], ENT_QUOTES, 'UTF-8');
        $fields['resourceLogoUrl'] = DCAExporter::getWebsiteUrl() . $fields['resourceLogoUrl'];
        $fields['citation'] = DCAExporter::getCredits() . ', ' . $fields['abbreviatedName'];
        // Needed to clean multiple addresses in the same field...
        $fields['sourceUrl'] = $this->_cleanSourceUrl($fields['sourceUrl']);
        return $fields;
    }

    private function _setFileName ()
    {
        if ($this->_fields['id'] != DCAExporter::$species2000Metadata['id']) {
            return $this->_fields['id'] . '.xml';
        }
        return 'col.xml';
    }
    
    private function _cleanSourceUrl ($url)
    {
        if (strpos($url, ';') !== false) {
            $url = substr($url, 0, strpos($url, ';'));
        }
        return $url;
    }

    private function _setSource ()
    {
        return DCAExporter::basePath() . '/templates/eml.tpl';
    }

    private function _setDestination ()
    {
        return $this->_dir . 'dataset/';
    }

    public function resetEmlDir ()
    {
        //$this->_removeDir($this->_dest);
        DCAExporter::removeDir($this->_dest);
        mkdir($this->_dest);
    }
    
    public function writeEml ()
    {
        $template = new Template($this->_src, $this->_dest);
        $template->decorate($this->_fields, 'xml');
        $template->writeFile($this->_fileName, 'eml');
        unset($template);
    }
}