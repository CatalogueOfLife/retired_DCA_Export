<?php
abstract class DCAExporterAbstract
{
    protected $_dbh;
    protected $_dir;
    protected $_del;
    protected $_sep;

    public function __construct ($dbh, $dir, $del, $sep)
    {
        $this->_dbh = $dbh;
        //var_dump($this->_dbh);
        $this->_dir = $dir;
        $this->_del = $del;
        $this->_sep = $sep;
    }

    protected function _createTextFile ($fileName)
    {
        if (!$this->_dir) {
            throw new Exception('Directory for export files has not been set!');
        }
        // Create new file
        $fh = fopen($this->_dir . $fileName, 'w');
        if (!$fh) {
            throw new Exception('Cannot create file ' . $fileName . '"');
        }
        fclose($fh);
    }
    
    protected function _openFileHandler ($fileName)
    {
        // Append to existing file
        $fh = fopen($this->_dir . $fileName, 'ab');
        if (!$fh) {
            throw new Exception('Cannot write to file ' . $fileName . '"');
        }
        return $fh;
    }
    
    protected function _closeFileHandler($fh) {
        @fclose($fh);
    }
    
    protected function _writeLine ($fh, array $fields)
    {
        // Reset delimiter and separator to defaults if necessary
        if ($this->_del == '') {
            $this->_del = ',';
        }
        if ($this->_sep == '') {
            $this->_sep = '"';
        }
        fputcsv($fh, $fields, $this->_del, $this->_sep);
       
    }
}