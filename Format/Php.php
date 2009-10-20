<?php
require_once 'SimpleApi/Format/Abstract.php';

class SimpleApi_Format_Php extends SimpleApi_Format_Abstract
{
    protected $_contentType = 'application/json';
    
    public function getContentType()
    {
        return $this->_contentType;
    }
    
    public function getOutput()
    {
        return serialize($this->_input);
    }
}