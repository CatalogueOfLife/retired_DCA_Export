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
        'title', 
        'groupName', 
        'authorsEditors', 
        'organizationName', 
        'contact', 
        'version', 
        'pubDate', 
        'abstract', 
        'numberOfSpecies', 
        'numberOfInfraspecies', 
        'numberOfSynonyms', 
        'numberOfCommonNames', 
        'totalNumber',
        'sourceUrl',
        'taxonomicCoverage',
        'contactCountry',
        'contactCity',
        'resourceLogoUrl'
    );
    private $_fields;
    private $_src;
    private $_dest;
    private $_taxonomicCoverageTemplate = 
                '<taxonomicClassification>
                    <taxonRankName>
                        [taxonRankName]
                    </taxonRankName>
                    <taxonRankValue>
                        [taxonRankValue]
                    </taxonRankValue>
                    <commonName>
                        [commonName]
                    </commonName>
                </taxonomicClassification>';

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
            $fields['dateStamp'] = date("c");
            return $fields;
        }
        $diff = array_diff($this->_keys, array_keys($fields));
        // Input array contains unexpected keys; throw exception;
        if (!empty($diff)) {
            throw new Exception(
                'Input array for EML file is incomplete or invalid!' . print_r(
                    $diff));
        }
        // Using fake data as taxonomic coverage is not yet available in 1.6
        // See _getTaxonomicCoverage method
        $fields['taxonomicCoverage'] = $this->_getTaxonomicCoverage($fields['groupName']);
        $fields['dateStamp'] = date("c"); 
        return $fields;
    }

    private function _setFileName ()
    {
        if ($this->_fields['id'] != DCAExporter::$species2000Metadata['id']) {
            return $this->_fields['id'] . '.xml';
        }
        return 'col.xml';
    }

    private function _setSource ()
    {
        return dirname(__FILE__) . '/../templates/eml.tpl';
    }

    private function _setDestination ()
    {
        return dirname(__FILE__) . '/../' . $this->_dir . 'dataset/';
    }

    public function resetEmlDir ()
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
    
    // Decorates [placeholders] in taxonomic coverage template _taxonomicCoverageTemplate
    private function _decorateTaxonomicCoverage (array $data)
    {
        $lines = count($data);
        $output = '';
        for ($i = 0; $i < $lines; $i++) {
            $output .= Template::decorateString(
                $this->_taxonomicCoverageTemplate, $data[$i]);
        }
        return $output;
    }
    
    private function _getTaxonomicCoverage ($id) 
    {
        // In v1.7 his function should get the points of attachment with a separate query
        // Currently in 1.6 all we have is the group name in English...
        // Therefore we use a mockup to set the group name and fake the group name
        // by passing it as the id
        // TODO: change ASAP when 1.7 is final!
       $mockup = array(
            array(
                'taxonRankName' => '',
                'taxonRankValue' => '',
                'commonName' => $id
            )
        );
        return $this->_decorateTaxonomicCoverage($mockup);
    }

    public function writeEml ()
    {
        $template = new Template($this->_src, $this->_dest);
        $template->decorate($this->_fields);
        $template->writeFile($this->_fileName, 'eml');
        unset($template);
    }
}