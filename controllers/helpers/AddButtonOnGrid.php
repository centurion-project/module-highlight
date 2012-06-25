<?php
class Highlight_Controller_Action_Helper_AddButtonOnGrid extends Zend_Controller_Action_Helper_Abstract
{
    public function postDispatch()
    {
        //We don't use it from now. But maybe later.
        return;
        
        if ($this->getActionController() instanceof Centurion_Rest_Controller_CRUD && $this->getRequest()->getActionName() === 'index') {
            $this->getResponse()->appendBody(
                $this->getActionController()->view->partial('crud/grid.phtml', 'highlight', array('model' => $this->getRequest()->getParam('model')))
            );
        }
        
        
    }
}