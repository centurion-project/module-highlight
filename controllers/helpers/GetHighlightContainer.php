<?php
class Highlight_Controller_Action_Helper_GetHighlightContainer extends Zend_Controller_Action_Helper_Abstract
{

    public function direct($containerName)
    {
        //TODO: change all call to action helper => view helper and remove this helper.
        return $this->getActionController()->view->getHighlightContainer($containerName);
    }
}