<?php
/**
 * Template
 * 
 * Class to create .xml or .eml files based on an existing template.
 * Assumes delimiter and separator to be described as [del] and [sep] in meta.tpl file
 * and [placeholders] in source_database_X.eml files (see models/SourceDatabase).
 * 
 * @author Ruud Altenburg
 */
class Template
{
    private $_file;
    private $_source;
    private $_destination;
    private $_requiredFileName = array(
        'xml' => array(
            'meta', 
            '.xml'
        ), 
        'eml' => array(
            '.xml'
        )
    );

    public function __construct ($source, $destination)
    {
        $this->_source = $source;
        $this->_destination = $destination;
        $this->_file = file_get_contents($this->_source);
        if (!$this->_file) {
            throw new Exception('Could not locate template file at ' . $this->_source);
        }
    }

    // Decorates [placeholders] in string; static version of decorate for external use
    public static function decorateString ($str, array $data)
    {
        foreach ($data as $placeholder => $text) {
            $str = str_replace("[$placeholder]", $text, $str);
        }
        return $str;
    }

    private function _validateFileName (array $required, $fileName)
    {
        foreach ($required as $str) {
            if (!strstr($fileName, $str)) {
                throw new Exception(
                    'File should contain ' . $str . ' in its name!');
            }
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

    public function writeFile ($fileName, $type = 'xml')
    {
        $fileCheck = $this->_requiredFileName[$type];
        $this->_validateFileName($fileCheck, $fileName);
        $fh = fopen($this->_destination . $fileName, 'w');
        if (!$fh) {
            throw new Exception(
                'Cannot create or write to ' . $this->_destination . $fileName);
        }
        fwrite($fh, $this->_file);
        fclose($fh);
    }

    // Decorates [placeholders] in template file
    public function decorate (array $data, $xml = false)
    {
        foreach ($data as $placeholder => $text) {
            $this->_file = str_replace("[$placeholder]", 
                $xml ? htmlspecialchars($text) : $text, $this->_file);
        }
    }

}
