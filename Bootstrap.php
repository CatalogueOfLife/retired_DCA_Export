<?php
class Bootstrap
{
    private $_dbh;
    private $_dir;
    private $_del;
    private $_sep;

    public function __construct (PDO $dbh, $dir, $del, $sep)
    {
        $this->_dbh = $dbh;
        $this->_dir = $dir;
        $this->_del = $del;
        $this->_sep = $sep;
        // Test writability export directory
        if (!is_writable($this->_dir)) {
            throw new Exception('Export directory "' . $this->_dir . '" is not writable!');
        }
        // Test delimiter
        if (!in_array($this->_del, array('', ';'))) {
            throw new Exception('Delimiter "' . $this->_del . '" is not a valid CSV delimiter!');
        }
        // Test separator
        if (!in_array($this->_sep, array('', '\''))) {
            throw new Exception('Separator "' . $this->_sep . '" is not a valid CSV separator!');
        }
        // Text files used to write to are created on the fly when the objects are created
        $this->_init(
            array(
                'taxon', 
                'vernacular', 
                'description', 
                'distribution', 
                'identifier', 
                'reference'
            ));
    }

    // Create text files
    private function _init (array $objects)
    {
        foreach ($objects as $object) {
            $objectName = ucfirst($object);
            $$object = new $objectName($this->_dbh, $this->_dir, $this->_sep, $this->_del);
            $$object->init();
            unset($$object);
        }
    }

}
