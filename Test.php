<?php
require_once 'SimpleApi/Abstract.php';

class SimpleApi_Test extends SimpleApi_Abstract
{
    protected $_actionRules = array(
        'sum' => array(
            'params' => array(
                'num1' => array(
                    'required' => true, 
                    'type' => 'numeric'
                ),
                'num2' => array(
                    'required' => true, 
                    'type' => 'numeric'
                )
            ), 
            'method' => 'get'
        ),
        'diff' =>array(
            'params' => array(
                'num1' => array('dependencies' => array('num2'))
            )
        )
    );
    
    public function sumAction()
    {
        $num1 = (float) $this->_requestParams['num1'];
        $num2 = (float) $this->_requestParams['num2'];
        $sum = $num1 + $num2;
        
        return array('sum' => $sum);
    }
    
    public function diffAction()
    {
        $num1 = (float) $this->_requestParams['num1'];
        $num2 = (float) $this->_requestParams['num2'];
        $difference = $num1 - $num2;
        
        return array('difference' => $difference);
    }
}