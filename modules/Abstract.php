<?php
abstract class DCAModuleAbstract
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

    private function _cleanString ($str)
    {
        // Characters to remove
        $delete = array("\r", "\n", "\r\n", "\\");
        // Characters to transfer to space
        $space = array("\t" );
        // Characters to find...
        $find = array('""');
        //... and replace with...
        $replace = array('"');
        return str_replace($find, $replace,
            str_replace($space, ' ',
            str_replace($delete, '', $str)
        ));
    }

    protected function _getCredits () {
        return 'Species 2000 & ITIS Catalogue of Life: ' . $this->_getReleaseDate();
    }

    protected function _getReleaseDate ()
    {
        $query = 'SELECT `edition` FROM `_credits` WHERE `current` = 1';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['edition'] : false;
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
        //fputcsv($fh, $fields, $this->_del, $this->_sep);
        $this->fputcsv2($fh, $fields, $this->_del, $this->_sep);
    }
    
    protected function _getNaturalKey ($id)
    {
        $query = 'SELECT `hash`
                  FROM `_natural_keys`
                  WHERE `id` = ?';
        $stmt = $this->_dbh->prepare($query);
        $stmt->execute(array(
            (int)$id
        ));
        return $stmt->fetchColumn();
    }

    public function writeHeader ()
    {
        $this->_writeLine($this->_fh, $this->fields);
    }

    public function decorate (array $row)
    {
        foreach ($row as $p => $v) {
            if (property_exists($this, $p)) {
                $this->$p = $v;
            }
        }
    }

    /*
     * Replacement function for fputcsv that actually works!
     */
    public function fputcsv2 ($fh, array $fields, $del = ',', $sep = '"', $mysql_null = false)
    {
        $del_esc = preg_quote($del, '/');
        $sep_esc = preg_quote($sep, '/');

        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $mysql_null) {
                $output[] = 'NULL';
                continue;
            }
            $field = $this->_cleanString($field);
            $output[] = preg_match("/(?:${del_esc}|${sep_esc}|\s)/", $field) ? ($sep . str_replace(
                $sep, $sep . $sep, $field) . $sep) : $field;
        }

        fwrite($fh, join($del, $output) . "\n");
    }
}