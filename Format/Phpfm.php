<?php
require_once 'SimpleApi/Format/Abstract.php';

class SimpleApi_Format_Phpfm extends SimpleApi_Format_Abstract
{
    protected $_contentType = 'text/html; charset=utf-8';
    
    public function getContentType()
    {
        return $this->_contentType;
    }
    
    public function getOutput()
    {
        return '<pre>' . serialize($this->_input) . '</pre>';
    }
}