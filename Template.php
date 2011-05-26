<?php
/**
 * Template
 * 
 * Class to create meta.xml based on existing template.
 * Assumes delimiter and separator to be described as [del] and [sep] in meta.tpl file
 * 
 * @author Ruud Altenburg
 */
class Template
{
    private $_file;
    private $_source;
    private $_destination;

    public function __construct ($source, $destination)
    {
        $this->_source = $source;
        $this->_destination = $destination;
        $this->_file = file_get_contents($this->_source);
        if (!$this->_file) {
            throw new Exception('Could not locate template file at ' . $this->_source);
        }
        // Simple check to validate if meta.tpl is valid
        // @TODO: input may require better validation
        if (!strstr(
            $this->_file, 'rs.tdwg.org')) {
            throw new Exception('Invalid meta.xml template file');
        }
    }

    public function setDelimiter ($del)
    {
        if ($del == "\t") {
            $del = '\t';
        }
        $this->_file = str_replace('[del]', $del, $this->_file);
    }

    public function setSeparator ($sep)
    {
        $this->_file = str_replace('[sep]', $sep, $this->_file);
    }

    public function writeFile ($fileName)
    {
        $this->_validateFileName($fileName);
        $fh = fopen($this->_destination . $fileName, 'w');
        if (!$fh) {
            throw new Exception('Cannot create or write to ' . $this->_destination . $fileName);
        }
        fwrite($fh, $this->_file);
        fclose($fh);
    }

    private function _validateFileName ($fileName)
    {
        $required = array(
            'meta', 
            '.xml'
        );
        foreach ($required as $str) {
            if (!strstr($fileName, $str)) {
                throw new Exception(
                    'File should contain ' . $str . ' in its name!');
            }
        }
    }
}
