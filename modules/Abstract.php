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
        $this->_dir = $dir;
        $this->_del = $del;
        $this->_sep = $sep;
    }

    public function decorate (array $row)
    {
        foreach ($row as $p => $v) {
            if (property_exists($this, $p)) {
                $this->$p = $v;
            }
        }
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
        fwrite($fh, "\xEF\xBB\xBF");
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

    protected function _closeFileHandler ($fh)
    {
        @fclose($fh);
    }

    protected function _writeLine ($fh, array $fields)
    {
        fputcsv($fh, $fields, $this->_del, $this->_sep);
    }
}