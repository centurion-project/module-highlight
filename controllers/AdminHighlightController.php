<?php 

class Highlight_AdminHighlightController extends Highlight_Model_Controller_AdminHighlightController
{

    public function init()
    {
        $this->_helper->authCheck();
        $this->_helper->aclCheck();
        parent::init();
    }
    
}
