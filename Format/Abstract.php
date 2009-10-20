<?php
abstract class SimpleApi_Format_Abstract
{
    protected $_input;
    protected $_output;
    
    abstract public function getOutput();
    abstract public function getContentType();
    
    public function __construct($input)
    {
        $this->_input = $input;
        $this->_output = $this->getOutput();
    }
    
    public function output()
    {
        header('Content-Type: ' . $this->getContentType());
        echo $this->_output;
        exit;
    }
}