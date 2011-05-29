<?php
/**
 * Zip
 * 
 * Class to zip file or contents of a directory.
 * 
 * @author Ruud Altenburg, extended and improved from 
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
        $res = $zip->open($this->_destination, ZIPARCHIVE::CREATE);
        if (!$res) {
            unset($zip);
            throw new Exception('Cannot create zip archive ' . $this->_destination .': ' .$res);
        } 
        /* TODO: skip creating archive if it already exists, or delete first and continue?
        else {
            if (file_exists($this->_destination)) {
                unlink($this->_destination);
            }
            $res = $zip->open($this->_destination, ZIPARCHIVE::CREATE);
        } */
        return $zip;
    }
    
    private function _disableTimeOut() {
        set_time_limit(0);
    }
    
    public function createArchive ($source, $destination)
    {
        // Needed for really large archives
        $this->_disableTimeout();
        $zip = $this->_initZip($source, $destination);
        $this->_source = realpath($this->_source);
        if (is_dir($this->_source)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->_source), 
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                // Skip hidden files and directories
                $fileOrDir = str_replace($this->_source .'/', '', $file);
                if ($fileOrDir[0] != '.') {
                    $path = realpath($file);
                    if (is_dir($path)) {
                        $zip->addEmptyDir(str_replace($this->_source .'/', '', $path .'/'));
                    }
                    else if (is_file($path)) {
                        $zip->addFile($path, $fileOrDir);
                    }
                }
            }
        } 
        else if (is_file($this->_source)) {
            $zip->addFile($this->_source, basename($this->_source));
        }
        return $zip->close();
    }
}
