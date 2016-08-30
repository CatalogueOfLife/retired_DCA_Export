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

    public function __construct ($dbh, $del, $sep, $sc, $bl, $dir, $zip, $excluded)
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
            $this->_validateDir($zip);
            $this->_validateExcluded($excluded);
            $this->_setInternalCodingToUtf8();

            // Text files used to write to are created on the fly when the objects are created
            if (empty(
                $this->_errors)) {
                $this->_init(
                    array(
                        'Taxon',
                        'Vernacular',
                        'Reference',
                        'Distribution',
                        'Description',
                    	'SpeciesProfile'
                    )
                );
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

            if (file_exists($dir . 'monitor')) {
                // The monitor file should be rewritten at least every 30 minutes
                // If it's older than that, apparently the export has died
                // Recreate the temp folder if this is the case!
                if (time() - filemtime($dir . 'monitor') > 30 * 60) {
                    DCAExporter::removeDir($dir);
                    mkdir($dir);
                // Nothing wrong, issue notice and display ETA to the user
                } else {
                    $progress = '<br>Progress: ' . file_get_contents($dir . 'monitor') . '.<br>';
                    $this->_errors[3] = 'Export already initiated on ' .
                        date ("F d Y H:i:s", filemtime($dir)) . ' by a different user. ' .
                        (isset($progress) ? $progress : '') . 'Please retry downloading later!';
                }
            }

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
        // No search critera; return error
        if (empty($sc)) {
            $this->_errors[10] = 'No search criteria given.';
            return array();
        }
        // Complete dump; return reprocessed array
        if (in_array('[all]', $sc)) {
            return array('kingdom' => '[all]');
        }
        // Regular search pattern
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

    private function _validateExcluded ($excluded)
    {
    	if ($excluded && is_array($excluded)) {
    		foreach ($excluded as $id) {
    			if (!$this->_sourceDatabaseExists($id)) {
    				$this->_errors[] = 'Excluded source database id <b>' . $id . '</b> does not exist.';
    			}
    		}
    	}
    }

    private function _sourceDatabaseExists ($id)
    {
    	$query = 'SELECT `name` FROM `source_database` WHERE `id` = ?';
    	$stmt = $this->_dbh->prepare($query);
    	$stmt->execute(array(
    		$id
    	));
    	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	return $res ? true : false;
    }

    public function getErrors ()
    {
        return $this->_errors;
    }
}
