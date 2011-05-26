<?php
class Bootstrap
{
    // Database handler
    private $_dbh;
    // Directory in which files are saved
    private $_dir;
    // CSV delimiter
    private $_del;
    // CSV separator
    private $_sep;
    // Search criteria
    private $_sc;
    // Collects bootstrap exceptions
    private $_errors = array();

    public function __construct ($dbh, $dir, $del, $sep, $sc)
    {
        $this->_dbh = $dbh;
        $this->_dir = $this->_validateDir($dir);
        $this->_del = $this->_validateDel($del);
        $this->_sep = $this->_validateSep($sep);
        $this->_sc = $this->_validateSc($sc);
        
        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                throw new Exception($error);
            }
        }
        
        // Text files used to write to are created on the fly when the objects are created
        $this->_init(
            array(
                'taxon', 
                'vernacular',
                'reference' /*, 
                'description', 
                'distribution', 
                'identifier' 
                */
            ));
    }

    // Create text files
    private function _init (array $objects)
    {
        foreach ($objects as $object) {
            $objectName = ucfirst($object);
            $$object = new $objectName($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            $$object->init();
            $$object->writeHeader();
            unset($$object);
        }
    }

    private function _validateDir ($dir)
    {
        if (!is_writable($dir)) {
            $this->_errors[] = 'Export directory "' . $dir . '" is not writable!';
        }
        return $dir;
    }

    private function _validateDel ($del)
    {
        if (!in_array($del, array(
            ',', 
            ';', 
            chr(9)
        ))) {
            $this->_errors[] = 'Delimiter "' . $del . '" is not a valid CSV delimiter!';
        }
        return $del;
    }

    private function _validateSep ($sep)
    {
        if (!in_array($sep, array(
            '"', 
            '\'',
            chr(0)
        ))) {
            $this->_errors[] = 'Delimiter "' . $sep . '" is not a valid CSV separator!';
        }
        return $sep;
    }

    // @TODO: implement filter
    private function _validateSc ($sc)
    {
        return $sc;
    }

}
