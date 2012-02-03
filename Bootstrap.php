<?php
class DCABootstrap
{
    private $_dbh;
    private $_dir;
    private $_del;
    private $_sep;
    private $_sc;
    private $_bl;
    
    private $_errors = array();

    public function __construct ($dbh, $del, $sep, $sc, $bl, $dir, $filePaths)
    {
        $this->_dbh = $dbh;
        if (!($this->_dbh instanceof PDO)) {
            $this->_errors[1] = 'Could not create database instance; check settings in settings.ini!';
        }
        else {
            $this->_dir = $this->_validateTempDir($dir);
            $this->_del = $this->_validateDel($del);
            $this->_sep = $this->_validateSep($sep);
            $this->_sc = $this->_validateSc($sc);
            $this->_bl = $this->_validateBl($bl);
            foreach ((array)$filePaths as $path) {
                $this->_validateDir($path);
            }
            $this->_setInternalCodingToUtf8();
            
            // Text files used to write to are created on the fly when the objects are created
            if (empty(
                $this->_errors)) {
                $this->_init(
                    array(
                        'taxon', 
                        'vernacular', 
                        'reference', 
                        'distribution'
                    ));
            }
        }
    }

    private function _setInternalCodingToUtf8 ()
    {
        if (mb_internal_encoding() != 'UTF-8') {
            mb_internal_encoding("UTF-8");
        }
    }

    // Create text files
    private function _init (array $models)
    {
        foreach ($models as $model) {
            $modelName = ucfirst($model);
            $$model = new $modelName($this->_dbh, $this->_dir, $this->_del, $this->_sep);
            $$model->init();
            $$model->writeHeader();
            unset($$model);
        }
    }

    private function _validateTempDir ($dir)
    {
        // First test if base directory is writable
        if (!is_writable(DCAExporter::basePath().'/'.DCAExporter::$dir)) {
            $this->_errors[2] = 'Directory "' . DCAExporter::basePath().'/'.DCAExporter::$dir . '" is not writable.';
        }
        // Test if temporary directory is present; 
        // if so, taxon is currently being exported by another user
        if (file_exists($dir)) {
            $this->_errors[3] = 'Export already initiated by another user. 
                Please retry download later.';
        // ... else create temporary directory to write to
        } else {
            mkdir($dir);
        }
        return $dir;
    }

    private function _validateDir ($dir)
    {
         if (!is_writable($dir)) {
            $this->_errors[] = 'Directory "' .$dir . '" is not writable.';
        }
        return $dir;
    }

    private function _validateDel ($del)
    {
        if (!in_array($del, array(
            ',', 
            ';', 
            "\t"
        ))) {
            $this->_errors[5] = 'Delimiter "' . $del . '" is not a valid CSV delimiter.';
        }
        return $del;
    }

    private function _validateSep ($sep)
    {
        if (!in_array($sep, array(
            '"', 
            '\'', 
            ''
        ))) {
            $this->_errors[6] = 'Delimiter "' . $sep . '" is not a valid CSV separator.';
        }
        return $sep;
    }

    private function _validateSc ($sc)
    {
        $filteredSc = array();
        foreach ($sc as $rank => $taxon) {
            if (empty($taxon)) {
                continue;
            }
            if (!in_array($rank, Taxon::$higherTaxa)) {
                $this->_errors[7] = 'Rank <b>' . $rank . '</b> is invalid.';
            }
            if ($taxon != '%' && !preg_match('/^[a-z0-9 ]+$/i', $taxon)) {
                $this->_errors[8] = 'Name <b>' . $taxon . '</b> contains invalid characters.';
            }
        }
        return $filteredSc;
    }

    private function _validateBl ($bl)
    {
        if (!in_array($bl, array(
            1, 
            2, 
            3
        ))) {
            $this->_errors[9] = 'Incorrect block level "' . $bl . '".';
        }
        return $bl;
    }

    public function getErrors ()
    {
        return $this->_errors;
    }
}
