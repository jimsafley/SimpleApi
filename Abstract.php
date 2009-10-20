<?php
require_once 'SimpleApi/Exception.php';
require_once 'SimpleApi/Format.php';

abstract class SimpleApi_Abstract
{
    protected $_requestAction;
    protected $_requestActionKey = 'action';
    protected $_requestParams;
    protected $_requestFormat = 'xmlfm';
    protected $_requestFormatKey = 'format';
    protected $_format;
    protected $_actionRules = array();
    
    public function setRequestActionKey($key)
    {
        $this->_requestActionKey = (string) $key;
    }
    
    public function setRequestFormatKey($key)
    {
        $this->_requestFormatKey = (string) $key;
    }
    
    public function handle()
    {
        $request = $_REQUEST;
        
        // Set the request format.
        if (isset($request[$this->_requestFormatKey])) {
            $this->_requestFormat = $request[$this->_requestFormatKey];
        }
        
        if (!isset($request[$this->_requestActionKey]) 
            || !strlen($request[$this->_requestActionKey])) {
            $this->_exception("No action provided.");
        }
        
        // Set the request action.
        $this->_requestAction = $_REQUEST[$this->_requestActionKey];
        unset($request[$this->_requestActionKey]);
        
        // Set the request parameters.
        $this->_requestParams = $request;
        
        // Check for valid method.
        if (isset($this->_actionRules[$this->_requestAction]['method'])) {
            switch ($this->_actionRules[$this->_requestAction]['method']) {
                case 'post':
                    if (!isset($_POST[$this->_requestActionKey])) {
                        $this->_exception("The \"{$this->_requestAction}\" action must use the POST method.");
                    }
                    break;
                case 'get':
                    if (!isset($_GET[$this->_requestActionKey])) {
                        $this->_exception("The \"{$this->_requestAction}\" action must use the GET method.");
                    }
                    break;
                default:
                    break;
            }
        }
        
        // Check for required parameters and dependencies.
        if (isset($this->_actionRules[$this->_requestAction]['params'])) {
            $actionParams = $this->_actionRules[$this->_requestAction]['params'];
            foreach ($actionParams as $paramKey => $paramSettings) {
                if (!isset($this->_requestParams[$paramKey]) 
                    && isset($paramSettings['required']) 
                    && $paramSettings['required']) {
                    $this->_exception("The required \"$paramKey\" parameter is missing.");
                }
                // Only check dependencies if the parameter exists.
                if (isset($paramSettings['dependencies']) 
                    && isset($this->_requestParams[$paramKey])) {
                    foreach ($paramSettings['dependencies'] as $dependency) {
                        if (!array_key_exists($dependency, $this->_requestParams)) {
                            $this->_exception("The dependent \"$dependency\" parameter is missing.");
                        }
                    }
                }
                // Ideas for more types: NaN (not a number), array (serialized array)
                if (isset($paramSettings['type'])) {
                    switch ($paramSettings['type']) {
                        case 'numeric':
                            if (!is_numeric($this->_requestParams[$paramKey])) {
                                $this->_exception("The \"$paramKey\" parameter must be numeric.");
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        
        $actionMethodName = "{$this->_requestAction}Action";
        if (!method_exists($this, $actionMethodName)) {
            $this->_exception("The \"{$this->_requestAction}\" action is not supported.");
        }
        
        // Call the action method, set the response, cast to an array.
        $result = (array) $this->$actionMethodName();
        
        // Set and render the output.
        require_once 'SimpleApi/Format.php';
        try {
            $this->_format = SimpleApi_Format::factory($this->_requestFormat, $result);
            $this->_format->output();
            exit;
        } catch (SimpleApi_Format_Exception $e) {
            $this->_exception($e->getMessage());
        }
    }
    
    public function getFormatsAction()
    {
        $formats = SimpleApi_Format::getFormats();
        return array('formats' => $formats);
    }
    
    public function getActionsAction()
    {
        return array('actions' => $this->_getActions());
    }
    
    protected function _getActions()
    {
        $actions = array();
        foreach (get_class_methods($this) as $method) {
            if (preg_match('/(.+)Action$/', $method, $matches)) {
                $actions[] = $matches[1];
            }
        }
        return $actions;
    }
    
    protected function _exception($message)
    {
        try {
            SimpleApi_Format::factory($this->_requestFormat, array('error' => (string) $message))->output();
        // Set output format to XML if an unknown format was sent.
        } catch (SimpleApi_Format_Exception $e) {
            SimpleApi_Format::factory('xml', array('error' => (string) $e->getMessage()))->output();
        }
        exit;
    }
}
