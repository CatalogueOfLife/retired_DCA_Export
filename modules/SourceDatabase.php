<?php
/**
 * SourceDatabase
 *
 * This is a model different from all others, as it does not write the source database metadata
 * to a single text file but to multiple EML files in the eml directory using the Template class.
 * It does not implement the DCAModuleInterface.
 *
 * @author Ruud Altenburg
 */
class SourceDatabase extends DCAModuleAbstract
{
    private $_fileName;
    private $_fields;
    private $_src;
    private $_dest;
    protected $_dir;
    protected $_dbh;

    public function __construct (PDO $dbh, $dir, $fields = array())
    {
        $this->_dbh = $dbh;
        $this->_dir = $dir;
        $this->_fields = $this->_setFields($fields);
        $this->_fileName = $this->_setFileName();
        $this->_src = $this->_setSource();
        $this->_dest = $this->_setDestination();
    }

    private function _setFields (array $fields)
    {
        // Differentiate between CoL and other source database emls
        if (empty($fields)) {
            $fields = $this->_setColFields();
        } else if (isset($fields['id'])) {
            $this->_setSourceDbFields($fields);
        } else {
            $this->_setColMetaFields($fields);
        }
        // Calculated/shared fields
        $fields['dateStamp'] = date("c");
        $fields['pubDate'] = date("Y-m-d", strtotime($this->_getReleaseDate()));
        $fields['packageId'] =
            (isset($fields['id']) ? $fields['id'] . ':' : '') . date("Y-m-d");
        // Needed to clean multiple addresses in the same field...
        if (isset($fields['sourceUrl'])) {
            $fields['sourceUrl'] = $this->_cleanSourceUrl($fields['sourceUrl']);
        }
        return $fields;
    }

    private function _setColFields ()
    {
        $fields = array(
            'id' => 'col',
            'title' => 'Catalogue of Life',
            'abbreviatedName' => 'Catalogue of Life',
            'groupName' => '',
            'organizationName' => 'Species 2000',
            'citation' => $this->_getCredits() . ', ' . 'Catalogue of Life'
        );
        return array_merge($fields, DCAExporter::getColEmlIni());
    }

    private function _setColMetaFields (&$fields)
    {
        $colEml = DCAExporter::getColEmlIni();
        $fields['year'] = date("Y");
        $fields['citation'] = $this->_getCredits() . ', ' . 'Catalogue of Life';
        $fields['resourceLogoUrl'] = DCAExporter::getWebsiteUrl() . $colEml['resourceLogoUrl'];
        $fields['sourceUrl'] = $colEml['sourceUrl'];
        $fields = array_merge($fields, DCAExporter::getColMetaEmlIni());
        return $fields;
    }

    private function _setSourceDbFields (&$fields)
    {
        $fields['abstract'] = htmlspecialchars($fields['abstract'], ENT_QUOTES, 'UTF-8');
        $fields['resourceLogoUrl'] = DCAExporter::getWebsiteUrl() . $fields['resourceLogoUrl'];
        $fields['citation'] = $this->_getCredits() . ', ' . $fields['abbreviatedName'];
        return $fields;
    }

    private function _setFileName ()
    {
        // Normal source database
        if (isset($this->_fields['id'])) {
            return $this->_fields['id'] . '.xml';
        // Meta eml
        } else if (isset($this->_fields['issn'])) {
            return 'eml.xml';
        // CoL eml
        } else {
            return 'col.xml';
        }
    }

    private function _setSource ()
    {
        // Meta eml
        if (isset($this->_fields['issn'])) {
            return DCAExporter::basePath() . '/templates/meta_eml.tpl';
        // Normal source database or CoL eml
        } else {
            return DCAExporter::basePath() . '/templates/eml.tpl';
        }
    }

    private function _setDestination ()
    {
        // Meta eml
        if (isset($this->_fields['issn'])) {
            return $this->_dir;
        // Normal source database or CoL eml
        } else {
            return $this->_dir . 'dataset/';
        }
    }

    public function resetEmlDir ()
    {
        DCAExporter::removeDir($this->_dest);
        mkdir($this->_dest);
    }

    public function writeEml ()
    {
        $template = new Template($this->_src, $this->_dest);
        $template->decorate($this->_fields, 'xml');
        $template->writeFile($this->_fileName, 'eml');
        unset($template);
    }

    private function _cleanSourceUrl ($url)
    {
        if (strpos($url, ';') !== false) {
            $url = substr($url, 0, strpos($url, ';'));
        }
        return $url;
    }
}