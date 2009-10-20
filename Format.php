<?php
require_once 'SimpleApi/Format/Exception.php';

class SimpleApi_Format
{
    public static function factory($format, $input)
    {
        $format = ucfirst(strtolower($format));
        if (!file_exists(dirname(__FILE__) . "/Format/$format.php")) {
            throw new SimpleApi_Format_Exception("Unknown output format.");
        }
        require_once "SimpleApi/Format/$format.php";
        $className = "SimpleApi_Format_$format";
        return new $className($input);
    }
    
    public static function getFormats()
    {
        $dir = new DirectoryIterator(dirname(__FILE__) . '/Format');
        $formats = array();
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isFile() 
                && $fileInfo != 'Abstract.php' 
                && $fileInfo != 'Exception.php') {
                $formats[] = str_replace('.php', '', strtolower($fileInfo->getFilename()));
            }
        }
        return $formats;
    }
}