<?php
interface DCAModuleInterface
{
    public function init();
    public function writeHeader();
    public function writeModel();
}