<?php
require_once 'SimpleApi/Format/Abstract.php';

class SimpleApi_Format_Jsonfm extends SimpleApi_Format_Abstract
{
    protected $_contentType = 'text/html; charset=utf-8';
    
    public function getContentType()
    {
        return $this->_contentType;
    }
    
    public function getOutput()
    {
        return '<pre>' . json_encode($this->_input) . '</pre>';
    }
}