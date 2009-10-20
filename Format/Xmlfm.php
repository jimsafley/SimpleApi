<?php
require_once 'SimpleApi/Format/Abstract.php';

class SimpleApi_Format_Xmlfm extends SimpleApi_Format_Abstract
{
    protected $_contentType = 'text/html; charset=utf-8';
    
    public function getContentType()
    {
        return $this->_contentType;
    }
    
    public function getOutput()
    {
        require_once 'SimpleApi/Format/Xml.php';
        $xml = new SimpleApi_Format_Xml($this->_input);
        return nl2br(str_replace(' ', '&nbsp;', preg_replace('/<([^<]+)>/', '&lt;$1&gt;', $xml->getOutput())));
    }
}