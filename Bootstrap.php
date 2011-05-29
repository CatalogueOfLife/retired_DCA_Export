<?php
class Bootstrap
{
    private $_dbh;
    private $_dir;
    private $_del;
    private $_sep;
    private $_sc;
    
    private $_errors = array();

    public function __construct ($dir, $del, $sep, $sc)
    {
        $this->_createDbInstance('db');
        $this->_dbh = DbHandler::getInstance('db');
        if (!($this->_dbh instanceof PDO)) {
            $this->_errors[] = 'Could not create database instance; check settings in settings.ini!';
        } else {
            $this->_dir = $this->_validateDir($dir);
            $this->_del = $this->_validateDel($del);
            $this->_sep = $this->_validateSep($sep);
            $this->_sc = $this->_validateSc($sc);
            
            // Text files used to write to are created on the fly when the objects are created
            // @TODO when standard has settled add 'description', 'distribution'
            if (empty($this->_errors)) {
                $this->_init(
                array(
                    'taxon', 
                    'vernacular', 
                    'reference'
                ));
            }
        }
    }

    private function _createDbInstance ($name)
    {
        $ini = DCAExporter::getIni();
        $config = $ini['db'];
        $dbOptions = array();
        if (isset($config["options"])) {
            $options = explode(",", $config["options"]);
            foreach ($options as $option) {
                $pts = explode("=", trim($option));
                $dbOptions[$pts[0]] = $pts[1];
            }
            DbHandler::createInstance($name, $config, $dbOptions);
        }
    }

    public function getDbHandler ()
    {
        return $this->_dbh;
    }

    public function getErrors ()
    {
        return $this->_errors;
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
            $this->_errors[] = 'Export directory "' . $dir . '" is not writable.';
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
            $this->_errors[] = 'Delimiter "' . $del . '" is not a valid CSV delimiter.';
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
            $this->_errors[] = 'Delimiter "' . $sep . '" is not a valid CSV separator.';
        }
        return $sep;
    }

    // @TODO: refine input filter
    private function _validateSc ($sc)
    {
        foreach ($sc as $rank => $taxon) {
            if (!in_array($rank, Taxon::$higherTaxa)) {
                $this->_errors[] = 'Rank <b>'.$rank.'</b> is invalid.';
            }
            if ($taxon != '%' && !ereg("[a-zA-Z]+", $taxon)) {
                $this->_errors[] = 'Name <b>'. $taxon.'</b> contains invalid characters.';
            }
        }
        return $sc;
    }
}
