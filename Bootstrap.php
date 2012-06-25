<?php 
class Highlight_Bootstrap extends Centurion_Application_Module_Bootstrap
{
    public function _initHelper()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new Highlight_Controller_Action_Helper_AddButtonOnGrid()
        );
    }
}