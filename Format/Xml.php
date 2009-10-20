<?php
require_once 'SimpleApi/Format/Abstract.php';

class SimpleApi_Format_Xml extends SimpleApi_Format_Abstract
{
    protected $_contentType = 'text/xml; charset=utf-8';
    protected $_xmlWriter;
    protected $_anonymousNodeName = 'node';
    
    public function getOutput()
    {
        $this->_xmlWriter = new XMLWriter;
        $this->_xmlWriter->openMemory();
        $this->_xmlWriter->setIndent(true);
        $this->_xmlWriter->setIndentString('    ');
        $this->_xmlWriter->startDocument('1.0', 'UTF-8');
        $this->_xmlWriter->startElement('api');
        $this->_fromArray($this->_input);
        $this->_xmlWriter->endElement();
        $this->_xmlWriter->endDocument();
        return $this->_xmlWriter->outputMemory();
    }
    
    public function getContentType()
    {
        return $this->_contentType;
    }
    
    public function setAnonymousNodeName($name)
    {
        $this->_anonymousNodeName = (string) $name;
    }
    
    protected function _fromArray($array) {
        if (is_array($array)) {
            foreach ($array as $index => $element) {
                if (is_array($element)) {
                    $this->_xmlWriter->startElement($this->_getElementName($index));
                    $this->_fromArray($element);
                    $this->_xmlWriter->endElement();
                } else {
                    $this->_setElement($index, $element);
                }
            }
        }
    }
    
    protected function _setElement($elementName, $elementText){
        $this->_xmlWriter->startElement($this->_getElementName($elementName));
        $this->_xmlWriter->text($elementText);
        $this->_xmlWriter->endElement();
    }
    
    protected function _getElementName($name)
    {
        // Set an arbitrary element name if the key is numeric.
        if (!is_string($name)) {
            $name = $this->_anonymousNodeName;
        }
        return $name;
    }
}