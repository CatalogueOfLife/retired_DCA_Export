<?php
/**
 * Zip
 * 
 * Class to zip file or contents of a directory.
 * 
 * @author Ruud Altenburg, based on 
 * http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
 */
class Zip
{
    protected $_source;
    protected $_destination;

    // Checks essential parameters
    // Not set to contructor, so the same instance can be reused
    private function _initZip ($source, $destination)
    {
        $this->_source = $source;
        $this->_destination = $destination;
        if (!extension_loaded('zip')) {
            throw new Exception('Zip extension is not loaded');
        }
        if (!file_exists($this->_source)) {
            throw new Exception('Path to source ' . $this->_source . ' is invalid');
        }
        $zip = new ZipArchive();
        if (!$zip->open($this->_destination, ZIPARCHIVE::CREATE)) {
            unset($zip);
            throw new Exception('Cannot create zip archive ' . $this->_destination);
        }
        return $zip;
    }

    public function createArchive ($source, $destination)
    {
        $zip = $this->_initZip($source, $destination);
        $this->_source = realpath($this->_source);
        if (is_dir($this->_source)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->_source), 
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                $file = realpath($file);
                if (is_dir($file)) {
                    $zip->addEmptyDir(str_replace($this->_source .'/', '', $file .'/'));
                }
                else if (is_file($file)) {
                    $zip->addFromString(str_replace($this->_source . '/', '', $file), file_get_contents($file));
                }
            }
        } 
        else if (is_file($this->_source)) {
            $zip->addFromString(basename($this->_source), file_get_contents($this->_source));
        }
        return $zip->close();
    }
}
